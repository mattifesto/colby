"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported SCCheckoutView */
/* global
    CBConvert,
    CBErrorHandler,
    CBMessageMarkup,
    CBModel,
    CBUI,
    CBUIPanel,
    CBUISection,
    CBUISectionItem4,
    CBUIStringEditor,
    CBUIStringsPart,
    Colby,
    ga,
    SCCartItem,
    SCShoppingCart,
    SCStripe,
    SCStripe_livePublishableKey,
    SCStripe_paymentsEnabled,
*/



var SCCheckoutView = {

    paymentIsProcessing: false,


    /**
     * This function is called from DOMContentDidLoad() if the shopping cart is
     * not empty.
     *
     * @return Promise
     */
    createOrder: function () {
        let promise = Colby.callAjaxFunction(
            "SCOrder",
            "create",
            {
                shoppingCart: {
                    className: "SCShoppingCart",
                    cartItems: SCShoppingCart.mainCartItemSpecs.getCartItems(),
                },
                shippingAddress: (
                    /* TODO: no shipping address should produce an error */
                    localStorage.shippingAddress ?
                    JSON.parse(localStorage.shippingAddress) :
                    ""
                ),
            }
        ).then(
            function (value) {
                createOrderWasFulfilled(value);
            }
        ).catch(
            function (error) {
                CBErrorHandler.displayAndReport(error);
            }
        );

        return promise;



        /* -- closures -- -- -- -- -- */

        /**
         * @closure in createOrder()
         *
         * @param object response
         *
         *      {
         *          approvedForNet30: bool
         *
         *              True if the customer should have the option to buy using
         *              Net30.
         *
         *          messageAsText: string
         *          orderArchiveId: ID
         *          orderSummaryHTML: string
         *          shippingInformationHTML: string
         *          orderDetails: object
         *
         *              {
         *                  orderRowId: int
         *                  orderTotalInCents: int
         *                  orderShippingChargeInCents: int
         *                  orderSalesTaxInCents: int
         *                  orderItems: [object]
         *              }
         *
         *          wasCancelled: bool
         *      }
         *
         * @return undefined
         */
        function createOrderWasFulfilled(response) {
            if (response.wasCancelled) {
                CBUIPanel.displayText(
                    response.messageAsText,
                    "View Cart >"
                ).then(
                    function () {
                        window.location.href = "/view-cart/";
                    }
                ).catch(
                    function (error) {
                        CBErrorHandler.displayAndReport(error);
                    }
                );

                return;
            }

            let element = SCCheckoutView.mainElement();

            if (SCStripe_paymentsEnabled) {
                createStripePaymentForm();
            }

            if (response.approvedForNet30) {
                createNet30PaymentForm();
            }

            SCCheckoutView.orderArchiveId = response.orderArchiveId;

            /* order summary */

            element.appendChild(
                createOrderSummaryElement(
                    response.orderSummaryHTML
                )
            );


            /* order CBMessages */

            let orderCBMessages = CBModel.valueToArray(
                response,
                "orderCBMessages"
            );

            if (orderCBMessages.length > 0) {
                let sectionContainerElement = CBUI.createElement(
                    "CBUI_sectionContainer"
                );

                let sectionElement = CBUI.createElement(
                    "CBUI_section"
                );

                sectionContainerElement.appendChild(
                    sectionElement
                );

                orderCBMessages.forEach(
                    function (cbmessage) {
                        let cbmessageElement = CBUI.createElement(
                            "CBContentStyleSheet"
                        );

                        cbmessageElement.innerHTML = (
                            CBMessageMarkup.messageToHTML(
                                cbmessage
                            )
                        );

                        sectionElement.appendChild(cbmessageElement);
                    }
                );

                element.appendChild(sectionContainerElement);
            }


            /* shipping information */

            element.appendChild(
                createShippingInformationElement(
                    response.shippingInformationHTML
                )
            );

            /**
             * Save order details object for when the user completes payment.
             */

            SCCheckoutView.orderDetails = response.orderDetails;

            SCCheckoutView.mainElement().appendChild(
                SCCheckoutView.orderDetailsElement(response.orderDetails)
            );

            SCCheckoutView.mainElement().appendChild(
                createPaymentSecurityInformationElement()
            );
        }
        /* createOrderWasFulfilled() */



        /**
         * @closure in createOrder()
         *
         * @return undefined
         */
        function createNet30PaymentForm() {
            let containerElement = (
                SCCheckoutView.paymentFormsContainerElement()
            );


            /* title */

            let titleElement = CBUI.createElement(
                "CBUI_title1"
            );

            containerElement.appendChild(
                titleElement
            );

            titleElement.textContent = "Pay Using Net 30";


            /* button */

            let elements = CBUI.createElementTree(
                "CBUI_container1",
                "CBUI_button1"
            );

            containerElement.appendChild(
                elements[0]
            );

            let buttonElement = elements[1];
            buttonElement.textContent = "Pay with Net 30 Now";

            buttonElement.addEventListener(
                "click",
                function () {
                    SCCheckoutView.payWithNet30();
                }
            );

            return;
        }
        /* createNet30PaymentForm() */



        /**
         * @closure in createOrder()
         *
         * @return undefined
         */
        function createOrderSummaryElement(orderSummaryHTML) {
            let element = document.createElement("div");

            let titleElement = CBUI.createElement(
                "CBUI_title1"
            );

            titleElement.textContent = "Order Summary";

            element.appendChild(
                titleElement
            );

            let section = CBUISection.create();

            element.appendChild(section.element);

            let sectionItem = document.createElement("div");
            sectionItem.className = "SCCheckoutView_orderSummary";
            sectionItem.innerHTML = orderSummaryHTML;

            section.element.appendChild(sectionItem);

            element.appendChild(
                CBUI.createHalfSpace()
            );

            return element;
        }
        /* createOrderSummaryElement() */



        /**
         * @closure in createOrder()
         *
         * @return undefined
         */
        function createPaymentSecurityInformationElement() {
            let sectionContainerElement = CBUI.createElement(
                "CBUI_sectionContainer"
            );

            let sectionElement = CBUI.createElement(
                "CBUI_section"
            );

            sectionContainerElement.appendChild(sectionElement);

            let actionElement = CBUI.createElement(
                "CBUI_action"
            );

            sectionElement.appendChild(actionElement);

            actionElement.textContent = "Payment Security Information";

            actionElement.addEventListener(
                "click",
                function () {
                    SCCheckoutView.showSecurityDetailsPanel();
                }
            );

            return sectionContainerElement;
        }
        /* createPaymentSecurityInformationElement() */



        /**
         * @closure in createOrder()
         *
         * @return undefined
         */
        function createShippingInformationElement(shippingInformationHTML) {
            let element = document.createElement("div");

            let titleElement = CBUI.createElement(
                "CBUI_title1"
            );

            titleElement.textContent = "Order Information";

            element.appendChild(
                titleElement
            );

            let section = CBUISection.create();

            element.appendChild(section.element);

            {
                let sectionItem = document.createElement("div");

                sectionItem.className =
                "SCCheckoutView_shippingInformation CBContentStyleSheet";

                sectionItem.innerHTML = shippingInformationHTML;

                section.element.appendChild(sectionItem);
            }

            {
                let sectionItem = CBUISectionItem4.create();
                sectionItem.callback = function () {
                    window.location.href = "/checkout/100/";
                };

                let stringsPart = CBUIStringsPart.create();
                stringsPart.string1 = "Edit";

                stringsPart.element.classList.add("action");

                sectionItem.appendPart(stringsPart);
                section.appendItem(sectionItem);
            }

            element.appendChild(
                CBUI.createHalfSpace()
            );

            return element;
        }
        /* createShippingInformationElement() */



        /**
         * @closure in createOrder()
         *
         * @return undefined
         */
        function createStripePaymentForm() {
            let parentElement = SCCheckoutView.paymentFormsContainerElement();

            let titleElement = CBUI.createElement(
                "CBUI_title1"
            );

            parentElement.appendChild(
                titleElement
            );

            titleElement.textContent = "Pay Using a Credit or Debit Card";

            let elements = CBUI.createElementTree(
                "CBUI_sectionContainer",
                "CBUI_section"
            );

            parentElement.appendChild(
                elements[0]
            );

            let sectionElement = elements[1];

            createItem_cardNumber();
            createItem_cardExpirationMonth();
            createItem_cardExpirationYear();
            createItem_cardVerificationCode();

            createItem_payNowButton();

            return;



            /**
             * @closure in createStripePaymentForm() in createOrder()
             *
             * @return undefined
             */
            function createItem_cardVerificationCode() {
                let stringEditor = CBUIStringEditor.create();
                stringEditor.title = "Card Verification Code";

                stringEditor.changed = function () {
                    SCCheckoutView.stripeCardVerificationCode = (
                        stringEditor.value
                    );
                };

                sectionElement.appendChild(
                    stringEditor.element
                );
            }
            /* createItem_cardVerificationCode() */



            /**
             * @closure in createStripePaymentForm() in createOrder()
             *
             * @return undefined
             */
            function createItem_cardExpirationMonth() {
                let stringEditor = CBUIStringEditor.create();
                stringEditor.title = "Card Expiration Month (MM)";

                stringEditor.changed = function () {
                    SCCheckoutView.stripeCardExpirationMonth = (
                        stringEditor.value
                    );
                };

                sectionElement.appendChild(
                    stringEditor.element
                );
            }
            /* createItem_cardExpirationMonth() */



            /**
             * @closure in createStripePaymentForm() in createOrder()
             *
             * @return undefined
             */
            function createItem_cardExpirationYear() {
                let stringEditor = CBUIStringEditor.create();
                stringEditor.title = "Card Expiration Year (YYYY)";

                stringEditor.changed = function () {
                    SCCheckoutView.stripeCardExpirationYear = (
                        stringEditor.value
                    );
                };

                sectionElement.appendChild(
                    stringEditor.element
                );
            }
            /* createItem_cardExpirationYear() */



            /**
             * @closure in createStripePaymentForm() in createOrder()
             *
             * @return undefined
             */
            function createItem_cardNumber() {
                let stringEditor = CBUIStringEditor.create();
                stringEditor.title = "Card Number";

                stringEditor.changed = function () {
                    SCCheckoutView.stripeCardNumber = stringEditor.value;
                };

                sectionElement.appendChild(
                    stringEditor.element
                );
            }
            /* createItem_cardNumber() */



            /**
             * @closure in createStripePaymentForm() in createOrder()
             *
             * @return undefined
             */
            function createItem_payNowButton() {
                let elements = CBUI.createElementTree(
                    "CBUI_container1",
                    "CBUI_button1"
                );

                parentElement.appendChild(
                    elements[0]
                );

                let buttonElement = elements[1];
                buttonElement.textContent = "Pay with Card Now";

                buttonElement.addEventListener(
                    "click",
                    function () {
                        payUsingStripe();
                    }
                );
            }
            /* createItem_payNowButton() */

        }
        /* createStripePaymentForm() */



        /**
         * @closure in createStripePaymentForm()
         *
         * @return undefined
         */
        function payUsingStripe() {
            SCCheckoutView.payWithStripe();
        }
    },
    /* createOrder() */



    /**
     * @return undefined
     */
    DOMContentDidLoad: function() {
        let quantityOfItemsInCart = SCCartItem.itemsToQuantity(
            SCShoppingCart.mainCartItemSpecs.getCartItems()
        );

        if (quantityOfItemsInCart === 0) {
            let mainElement = SCCheckoutView.mainElement();
            let sectionContainerElement = CBUI.createElement(
                "CBUI_section_container"
            );

            mainElement.appendChild(sectionContainerElement);

            let sectionElement = CBUI.createElement(
                "CBUI_section CBUI_container3 CBUI_touch_height"
            );

            sectionContainerElement.appendChild(sectionElement);

            let messageElement = CBUI.createElement(
                "CBUI_content CBContentStyleSheet"
            );

            sectionElement.appendChild(messageElement);

            messageElement.innerHTML = CBMessageMarkup.messageToHTML(
                [
                    "--- center",
                    "Your shopping cart is currently empty.",
                    "---",
                ].join("\n")
            );
        } else {
            SCCheckoutView.createOrder();
        }
    },
    /* DOMContentDidLoad() */



    /**
     * @return Element
     */
    mainElement: function () {
        if (SCCheckoutView.cachedMainElement === undefined) {
            SCCheckoutView.cachedMainElement = document.getElementsByClassName(
                "SCCheckoutView"
            )[0];
        }

        return SCCheckoutView.cachedMainElement;
    },
    /* mainElement() */



    /**
     * @param object orderDetails
     *
     *      {
     *          orderItems: [object]
     *      }
     *
     * @return Element
     */
    orderDetailsElement: function (orderDetails) {
        let element = document.createElement("div");

        let titleElement = CBUI.createElement(
            "CBUI_title1"
        );

        titleElement.textContent = "Order Items";

        element.appendChild(
            titleElement
        );

        let orderItems = CBModel.valueToArray(orderDetails, "orderItems");

        orderItems.forEach(
            function (cartItemModel) {
                element.appendChild(
                    SCCartItem.createCheckoutViewElement(
                        cartItemModel
                    )
                );
            }
        );


        {
            let sectionContainerElement = CBUI.createElement(
                "CBUI_sectionContainer"
            );

            element.appendChild(sectionContainerElement);

            let sectionElement = CBUI.createElement(
                "CBUI_section"
            );

            sectionContainerElement.appendChild(sectionElement);

            let actionElement = CBUI.createElement(
                "CBUI_action"
            );

            sectionElement.appendChild(actionElement);

            actionElement.textContent = "Edit Shopping Cart";

            actionElement.addEventListener(
                "click",
                function () {
                    window.location = "/view-cart/";
                }
            );
        }

        return element;
    },
    /* orderDetailsElement() */



    /**
     * This is the click event handler for the "Pay with Net 30" button.
     *
     * @return Promise
     */
    payWithNet30: function () {
        if (SCCheckoutView.paymentIsProcessing) {
            return;
        }

        SCCheckoutView.paymentIsProcessing = true;

        let panel = CBUIPanel.displayBusyText(
            "Please wait while your payment is processed..."
        );

        let promise = Colby.callAjaxFunction(
            "SCOrder",
            "payWithNet30",
            {
                orderID: SCCheckoutView.orderArchiveId,
            }
        ).then(
            function (value) {
                return payWithNet30_onFulfilled(value);
            }
        ).catch(
            function (error) {
                return payWithNet30_onRejected(error);
            }
        );

        return promise;



        /* -- closures -- -- -- -- -- */



        /**
         * @param object ajaxResponse
         *
         * @return object
         */
        function payWithNet30_onFulfilled(
            ajaxResponse
        ) {
            SCCheckoutView.paymentIsProcessing = false;

            panel.hide();

            let promise = CBUIPanel.displayText(
                ajaxResponse.message
            );

            let paymentWasSuccessful = CBModel.valueToBool(
                ajaxResponse,
                "paymentWasSuccessful"
            );

            if (paymentWasSuccessful) {
                SCShoppingCart.empty();

                /**
                 * @TODO 2020_02_20
                 *
                 *      Record sale with Google Analytics
                 */

            }

            promise.then(
                function () {
                    window.location.href = "/";
                }
            );

            return ajaxResponse;
        }
        /* payWithNet30_onFulfilled() */



        /**
         * @return undefined
         */
        function payWithNet30_onRejected(error) {
            SCCheckoutView.paymentIsProcessing = false;

            panel.hide();

            Colby.reportError(error);

            CBUIPanel.displayText(
                error.message
            );
        }
        /* payWithNet30_onRejected() */

    },
    /* payWithNet30() */



    /**
     * This is the click event handler for the "Pay Now" button.
     *
     * @return Promise
     */
    payWithStripe: function () {
        if (SCCheckoutView.paymentIsProcessing) {
            return;
        }

        SCCheckoutView.paymentIsProcessing = true;

        let panel = CBUIPanel.displayBusyText(
            "Please wait while your payment is processed..."
        );

        let promise = payWithStripe_step1CreateToken(
        ).then(
            function (createTokenResponse) {
                return payWithStripe_step2ChargeCard(createTokenResponse);
            }
        ).then(
            function (chargeResponse) {
                return payWithStripe_step3CardWasCharged(chargeResponse);
            }
        ).catch(
            function (error) {
                return payWithStripe_wasRejected(error);
            }
        );

        return promise;



        /* -- closures -- -- -- -- -- */


        /**
         * @return Promise
         */
        function payWithStripe_step1CreateToken() {
            return SCStripe.call(
                {
                    apiURL: "https://api.stripe.com/v1/tokens",
                    apiKey: SCStripe_livePublishableKey,
                    apiArgs: {
                        "card[number]": CBModel.valueToString(
                            SCCheckoutView,
                            "stripeCardNumber"
                        ),
                        "card[exp_month]": CBModel.valueToString(
                            SCCheckoutView,
                            "stripeCardExpirationMonth"
                        ).trim(),
                        "card[exp_year]": CBModel.valueToString(
                            SCCheckoutView,
                            "stripeCardExpirationYear"
                        ).trim(),
                        "card[cvc]": CBModel.valueToString(
                            SCCheckoutView,
                            "stripeCardVerificationCode"
                        ).trim(),
                    },
                }
            );
        }
        /* payWithStripe_createToken() */



        /**
         * @return Promise
         */
        function payWithStripe_step2ChargeCard(createTokenResponse) {
            let promise = Colby.callAjaxFunction(
                "SCOrder",
                "charge",
                {
                    "orderID": SCCheckoutView.orderArchiveId,
                    "stripeToken": createTokenResponse.id,
                }
            );

            return promise;
        }
        /* payWithStripe_chargeCard() */



        /**
         * @return undefined
         */
        function payWithStripe_step3CardWasCharged(ajaxResponse) {
            SCCheckoutView.paymentIsProcessing = false;

            panel.hide();

            let promise = CBUIPanel.displayText(
                ajaxResponse.message
            );

            if (ajaxResponse.stripeChargeWasSuccessful) {
                SCShoppingCart.empty();

                promise.then(
                    function () {
                        window.location.href = "/";
                    }
                );

                if (typeof ga === "function") {
                    ga('require', 'ec');

                    var orderDetails = SCCheckoutView.orderDetails;

                    for (var index in orderDetails.orderItems) {
                        if (orderDetails.orderItems.hasOwnProperty(index)) {
                            var orderItem = orderDetails.orderItems[index];

                            ga(
                                'ec:addProduct',
                                {
                                    id: orderItem.code,
                                    name: SCCartItem.getTitle(orderItem),
                                    category: orderItem.groupTitle,
                                    price: CBConvert.centsToDollars(
                                        SCCartItem.getPriceInCents(orderItem)
                                    ),
                                    quantity: SCCartItem.getQuantity(orderItem),
                                }
                            );
                        }
                    }

                    ga(
                        'ec:setAction',
                        'purchase',
                        {
                            id: orderDetails.orderRowId,
                            revenue: CBConvert.centsToDollars(
                                orderDetails.orderTotalInCents
                            ),
                            tax: CBConvert.centsToDollars(
                                orderDetails.orderSalesTaxInCents
                            ),
                            shipping: CBConvert.centsToDollars(
                                orderDetails.orderShippingChargeInCents
                            ),
                        }
                    );

                    ga(
                        'send',
                        'event',
                        'purchase'
                    );
                }
            }
        }
        /* payWithStripe_step3CartWasCharged() */



        /**
         * @return undefined
         */
        function payWithStripe_wasRejected(error) {
            SCCheckoutView.paymentIsProcessing = false;

            panel.hide();

            Colby.reportError(error);

            CBUIPanel.displayText(
                error.message
            );
        }
        /* payWithStripe_wasRejected() */

    },
    /* payWithStripe() */



    /**
     * @return undefined
     */
    showSecurityDetailsPanel: function() {
        let cbmessage = `
            --- SCCheckoutView_leftAlignedPanel

                --- h1
                Security
                ---

                Our site is built on a foundation of security. We use the
                (Stripe (a https://stripe.com)) (https://stripe.com) payment
                infrastructure. Stripe is a credit card processing service that
                makes payments simple and secure for you and for us. Funded by
                the founders of PayPal, Stripe uses a simple model that allows
                us to quickly and securely accept your payment without
                transferring your credit card number to us.

                When you enter your credit card information on our site, your
                web browser communicates directly and securely with Stripe and
                then notifies us that a payment was authorized. This innovative
                security measure means that your credit card information is
                shared only with Stripe, whose first priority is to keep that
                information secure, and is not stored by or in any way at risk
                with us.

                Since we have to ship your products to you, we do see your
                address, so in addition to the security provided by Stripe, this
                site communicates securely with your computer for everything you
                do. Even for simple activities such as browsing our products,
                the traffic is encrypted and secure. You’ll notice that the
                secure browsing symbol in your web browser is enabled for all of
                the pages on our site.

                Even your shopping cart cart and shipping address is stored on
                your computer and only transferred to us when you checkout.

                We make every effort to continually improve our security with
                the latest technology to make shopping with us simple and safe.

                Thank you for your order.
            ---
        `;

        CBUIPanel.displayCBMessage(cbmessage);
    },
    /* showSecurityDetailsPanel() */



    /**
     * @return Element
     */
    paymentFormsContainerElement: function () {
        let className = "SCCheckoutView_paymentFormsContainer";

        return document.getElementsByClassName(className)[0];
    },
    /* paymentFormsContainerElement() */

};
/* SCCheckoutView */



Colby.afterDOMContentLoaded(
    function () {
        SCCheckoutView.DOMContentDidLoad();
    }
);
