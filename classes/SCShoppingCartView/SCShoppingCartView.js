"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* global
    CBConvert,
    CBErrorHandler,
    CBMessageMarkup,
    CBUI,
    CBUIPanel,
    Colby,
    SCCartItem,
    SCCartItemCartView,
    SCShoppingCart,
*/



(function () {

    let itemsContainerElement;



    Colby.afterDOMContentLoaded(
        afterDOMContentLoaded
    );



    /**
     * @return undefined
     */
    function afterDOMContentLoaded() {
        let shoppingCartViewElement = getViewElement();

        if (shoppingCartViewElement === undefined) {
            /**
             * This is not a page that diplays the shopping cart.
             */
            return;
        }

        let originalCartItemSpecs = (
            SCShoppingCart.mainCartItemSpecs.getCartItems()
        );

        if (originalCartItemSpecs.length === 0) {
            renderEmptyCart();
        } else {
            Colby.callAjaxFunction(
                "SCCartItem",
                "updateSpecs",
                {
                    originalCartItemSpecs: originalCartItemSpecs,
                }
            ).then(
                function (response) {
                    init_processResponse(response);
                }
            ).catch(
                function (error) {
                    CBUIPanel.displayAndReportError(error);
                }
            );
        }

        return;



        /* -- closures -- -- -- -- -- */

        /**
         * @param [object] updatedCartItemSpecs
         *
         * @return undefined
         */
        function init_processResponse(updatedCartItemSpecs) {
            if (!Array.isArray(updatedCartItemSpecs)) {
                throw new Error(
                    [
                        "The response from the request to update the cart item",
                        "specs does not contain an array of updated cart item ",
                        "specs.",
                    ].join(" ")
                );
            }

            if (updatedCartItemSpecs.length !== originalCartItemSpecs.length) {
                throw new Error(
                    [
                        "The updated cart item specs array should be the same",
                        "length as the original cart item specs array.",
                    ].join(" ")
                );
            }

            for (
                let index = 0;
                index < originalCartItemSpecs.length;
                index +=1
            ) {
                let originalCartItemSpec = originalCartItemSpecs[index];
                let updatedCartItemSpec = updatedCartItemSpecs[index];

                if (originalCartItemSpec === null) {
                    break;
                } else if (updatedCartItemSpec === null) {
                    originalCartItemSpec
                    .CBActiveObject
                    .deactivate();
                } else {
                    originalCartItemSpec
                    .CBActiveObject
                    .replace(
                        updatedCartItemSpec
                    );
                }
            }

            renderElements();
        }
        /* init_processResponse() */

    }
    /* afterDOMContentLoaded() */



    /**
     * @return Element
     */
    function createEmptyCartButtonElement() {
        let elements = CBUI.createElementTree(
            "CBUI_sectionContainer SCShoppingCartView_emptyCart",
            "CBUI_section",
            "CBUI_action"
        );

        let element = elements[0];
        let buttonElement = elements[2];

        buttonElement.textContent = "Empty Shopping Cart";

        buttonElement.addEventListener(
            "click",
            function () {
                CBUIPanel.confirmText(
                    "Are you sure you want to empty your shopping cart?"
                ).then(
                    function (wasConfirmed) {
                        if (wasConfirmed) {
                            SCShoppingCart.empty();

                            SCShoppingCart.mainCartSavePromise.then(
                                function () {
                                    location.reload();
                                }
                            );
                        }
                    }
                ).catch(
                    function (error) {
                        CBErrorHandler.report(error);
                    }
                );
            }
        );

        return element;
    }
    /* createEmptyCartButtonElement() */



    /**
     * @return Element
     */
    function createProceedToCheckoutSectionItemElement() {
        let element = CBUI.createElement(
            "CBUI_action",
            "a"
        );

        element.href = "/checkout/100/";
        element.textContent = "Proceed to Checkout >";

        return element;
    }
    /* createProceedToCheckoutSectionItemElement() */



    /**
     * @return Element
     */
    function createSubtotalSectionItemElement() {
        let elements = CBUI.createElementTree(
            "CBUI_container_sideBySide",
            "SCShoppingCartView_subtotalLabel CBUI_textColor2"
        );

        let element = elements[0];
        let labelElement = elements[1];

        labelElement.textContent = "Subtotal";

        return element;
    }
    /* createSubtotalSectionItemElement() */



    /**
     * @return Element
     */
    function getViewElement() {
        let viewElements = document.getElementsByClassName(
            "SCShoppingCartView"
        );

        /**
         * Only the first SCShoppingCartView element is ever used. There should
         * only be one.
         */
        return viewElements[0];
    }



    /**
     * @return undefined
     */
    function renderElements() {
        let viewElement = getViewElement();

        let subtotalValueElement1 = CBUI.createElement(
            "SCShoppingCartView_subtotalValue1"
        );

        let subtotalValueElement2 = CBUI.createElement(
            "SCShoppingCartView_subtotalValue2"
        );

        function updateSubtotal() {
            let subtotalInCents =
            SCShoppingCart.mainCartItemSpecs.getCartItems().reduce(
                function (accumulatedSubtotalInCents, currentCartItemSpec) {
                    return (
                        accumulatedSubtotalInCents +
                        SCCartItem.getSubtotalInCents(
                            currentCartItemSpec
                        )
                    );
                },
                0
            );

            let subtotalText = (
                "$" +
                CBConvert.centsToDollars(subtotalInCents)
            );

            subtotalValueElement1.textContent = subtotalText;
            subtotalValueElement2.textContent = subtotalText;
        }

        SCShoppingCart.mainCartItemSpecs.addEventListener(
            "somethingChanged",
            updateSubtotal
        );

        updateSubtotal();

        {
            let elements = CBUI.createElementTree(
                "CBUI_sectionContainer",
                "CBUI_section"
            );

            viewElement.appendChild(
                elements[0]
            );

            let sectionElement = elements[1];

            let subtotalSectionItemElement = (
                createSubtotalSectionItemElement()
            );

            sectionElement.appendChild(
                subtotalSectionItemElement
            );

            subtotalSectionItemElement.appendChild(
                subtotalValueElement1
            );

            sectionElement.appendChild(
                createProceedToCheckoutSectionItemElement()
            );
        }

        {
            viewElement.appendChild(
                CBUI.createHalfSpace()
            );

            itemsContainerElement = CBUI.createElement(
                "SCShoppingCartView_itemsContainer"
            );

            viewElement.appendChild(
                itemsContainerElement
            );

            viewElement.appendChild(
                CBUI.createHalfSpace()
            );
        }

        {
            let elements = CBUI.createElementTree(
                "CBUI_sectionContainer",
                "CBUI_section"
            );

            viewElement.appendChild(
                elements[0]
            );

            let sectionElement = elements[1];

            let subtotalSectionItemElement = (
                createSubtotalSectionItemElement()
            );

            sectionElement.appendChild(
                subtotalSectionItemElement
            );

            subtotalSectionItemElement.appendChild(
                subtotalValueElement2
            );

            sectionElement.appendChild(
                createProceedToCheckoutSectionItemElement()
            );


            viewElement.appendChild(
                createEmptyCartButtonElement()
            );
        }

        /**
         * Render the shopping cart view for each cart item.
         */
        SCShoppingCart.mainCartItemSpecs.getCartItems().forEach(
            function (activeCartItemSpec) {
                itemsContainerElement.appendChild(
                    SCCartItemCartView.createElement(
                        activeCartItemSpec
                    )
                );
            }
        );
    }
    /* renderElements() */



    /**
     * @return undefined
     */
    function renderEmptyCart() {
        let viewElement = getViewElement();

        let sectionContainerElement = CBUI.createElement(
            "CBUI_section_container"
        );

        viewElement.appendChild(
            sectionContainerElement
        );

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
    }
    /* renderEmptyCart() */

})();
