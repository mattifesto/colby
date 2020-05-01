"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported SCOrderInspector */
/* global
    CBConvert,
    CBMessageMarkup,
    CBModel,
    CBUI,
    CBUIBooleanSwitchPart,
    CBUIExpander,
    CBUINavigationView,
    CBUIPanel,
    CBUISectionItem4,
    CBUIStringEditor,
    CBUIStringsPart,
    CBUser,
    CBView,
    Colby,
    SCCartItem,

    SCOrderInspector_emailHTML,
    SCOrderInspector_emailText,
    SCOrderInspector_model,
    SCOrderInspector_orderID,
    SCOrderInspector_originalIsArchived,
    SCOrderInspector_userIsADeveloper,
    SCOrderInspector_wholesaleCustomerModel,
*/


var SCOrderInspector = {


    /**
     * @return Element
     *
     *      {
     *          captureWasManual: bool (readonly)
     *      }
     */
    createManualCaptureSectionItemElement: function () {
        let booleanSwitchPart = CBUIBooleanSwitchPart.create();

        booleanSwitchPart.changed = function () {
            closure_changed();
        };

        /* section item */

        let sectionItemElement = CBUI.createElement(
            "CBUI_sectionItem"
        );

        Object.defineProperty(
            sectionItemElement,
            "captureWasManual",
            {
                configurable: false,
                enumerable: false,
                get: function () { return booleanSwitchPart.value; },
            }
        );

        /* create elements */
        {
            /* section item -> text conainer */

            let textContainerElement = CBUI.createElement(
                "CBUI_container_topAndBottom CBUI_flexGrow"
            );

            sectionItemElement.appendChild(
                textContainerElement
            );

            /* section item -> text container -> title */

            let titleElement = CBUI.createElement();
            titleElement.textContent = "Manual";

            textContainerElement.appendChild(titleElement);

            /* section item -> text container -> description */

            let descriptionElement = CBUI.createElement(
                "CBUI_textColor2 CBUI_textSize_small"
            );

            descriptionElement.textContent = (
                "Select this if the payment has already been captured " +
                "manually on the Stripe website."
            );

            textContainerElement.appendChild(descriptionElement);

            /* section item -> boolean switch */

            sectionItemElement.appendChild(
                booleanSwitchPart.element
            );
        }
        /* create elements */

        return sectionItemElement;



        /**
         * @return undefined
         */
        function closure_changed() {
            if (booleanSwitchPart.value === true) {
                CBUIPanel.confirmText(`

                    Setting this option is only appropriate if you have manually
                    captured this order directly on the Stripe website. Are you
                    sure you want to use this option?

                `).then(
                    function (wasConfirmed) {
                        if (!wasConfirmed) {
                            booleanSwitchPart.value = false;
                        }
                    }
                ).catch(
                    function (error) {
                        CBUIPanel.displayAndReportError(error);
                    }
                );
            }
        }
        /* closure_changed() */

    },
    /* createManualCaptureSectionItemElement() */


    /**
     * @return Element
     */
    createNotesElement: function () {
        let element = CBUI.createElement();

        let notesSectionElement = CBUI.createElement("CBUI_section");

        /* title */
        {
            let titleElement = CBUI.createElement("CBUI_title1");
            titleElement.textContent = "Notes";

            element.appendChild(titleElement);
        }
        /* title */

        /* notes */
        {
            let noteModels = CBModel.valueToArray(
                SCOrderInspector_model,
                "notes"
            );

            let sectionContainerElement = CBUI.createElement(
                "CBUI_sectionContainer SCOrderInspector_hide"
            );

            element.appendChild(sectionContainerElement);

            sectionContainerElement.appendChild(notesSectionElement);

            noteModels.forEach(
                function (noteModel) {
                    notesSectionElement.appendChild(
                        CBView.create(
                            {
                                className: "CBNoteView",
                                note: noteModel,
                            }
                        ).element
                    );

                    notesSectionElement.parentElement.classList.remove(
                        "SCOrderInspector_hide"
                    );
                }
            );
        }
        /* notes */

        let noteEditor = CBUIStringEditor.create();
        noteEditor.title = "New Note";

        let addButtonElement = CBUI.createElement("CBUI_action");
        addButtonElement.textContent = "Add";

        let clearButtonElement = CBUI.createElement("CBUI_action");
        clearButtonElement.textContent = "Clear";

        /* editor */
        {
            let sectionContainerElement = CBUI.createElement(
                "CBUI_sectionContainer"
            );

            element.appendChild(sectionContainerElement);

            let sectionElement = CBUI.createElement("CBUI_section");

            sectionContainerElement.appendChild(sectionElement);

            sectionElement.appendChild(noteEditor.element);

            let sectionItemElement = CBUI.createElement(
                "CBUI_container_horizontalEqual"
            );

            sectionElement.appendChild(sectionItemElement);

            sectionItemElement.appendChild(clearButtonElement);
            sectionItemElement.appendChild(addButtonElement);
        }
        /* editor */

        addButtonElement.addEventListener(
            "click",
            function () {
                Colby.callAjaxFunction(
                    "SCOrder",
                    "addNote",
                    {
                        orderID: SCOrderInspector_model.ID,
                        text: noteEditor.value,
                    }
                ).then(
                    function (noteModel) {
                        noteEditor.value = "";

                        notesSectionElement.appendChild(
                            CBView.create(
                                {
                                    className: "CBNoteView",
                                    note: noteModel,
                                }
                            ).element
                        );

                        notesSectionElement.parentElement.classList.remove(
                            "SCOrderInspector_hide"
                        );
                    }
                ).catch(
                    function (error) {
                        CBUIPanel.displayAndReportError(error);
                    }
                );
            }
        );

        clearButtonElement.addEventListener(
            "click",
            function () {
                noteEditor.value = "";
            }
        );

        return element;
    },
    /* createNotesElement() */

    /**
     * @return Element
     */
    createOverviewElement: function () {
        let element = CBUI.createElement();

        let titleElement = CBUI.createElement("CBUI_title1");
        titleElement.textContent = "Overview";

        element.appendChild(titleElement);

        let sectionContainerElement = CBUI.createElement(
            "CBUI_sectionContainer"
        );

        element.appendChild(sectionContainerElement);

        let sectionElement = CBUI.createElement(
            "CBUI_section"
        );

        sectionContainerElement.appendChild(sectionElement);

        sectionElement.appendChild(
            createOverviewElement_createKeyValueElement(
                "Order Number",
                CBModel.valueAsInt(
                    SCOrderInspector_model,
                    "orderRowId"
                ) || 0
            )
        );

        sectionElement.appendChild(
            createOverviewElement_createKeyValueElement(
                "Order Date",
                Colby.dateToLocaleString(
                    new Date(
                        CBModel.valueAsInt(
                            SCOrderInspector_model,
                            "orderCreated"
                        ) * 1000
                    ),
                    {
                        compact: true,
                    }
                )
            )
        );

        sectionElement.appendChild(
            createOverviewElement_createKeyValueElement(
                "Subtotal",
                CBConvert.centsToDollars(
                    CBModel.valueAsInt(
                        SCOrderInspector_model,
                        "orderSubtotalInCents"
                    ) || 0
                )
            )
        );

        sectionElement.appendChild(
            createOverviewElement_createKeyValueElement(
                "Shipping",
                CBConvert.centsToDollars(
                    CBModel.valueAsInt(
                        SCOrderInspector_model,
                        "orderShippingChargeInCents"
                    ) || 0
                )
            )
        );

        sectionElement.appendChild(
            createOverviewElement_createKeyValueElement(
                "Sales Tax",
                CBConvert.centsToDollars(
                    CBModel.valueAsInt(
                        SCOrderInspector_model,
                        "orderSalesTaxInCents"
                    ) || 0
                )
            )
        );

        sectionElement.appendChild(
            createOverviewElement_createKeyValueElement(
                "Total",
                CBConvert.centsToDollars(
                    CBModel.valueAsInt(
                        SCOrderInspector_model,
                        "orderTotalInCents"
                    ) || 0
                )
            )
        );

        return element;


        /* -- closures -- -- -- -- -- */

        /**
         * @param string key
         * @param string value
         *
         * @return Element
         */
        function createOverviewElement_createKeyValueElement(key, value) {
            let sectionItemElement = CBUI.createElement(
                "CBUI_container_leftAndRight"
            );

            let keyElement = CBUI.createElement("CBUI_textColor2");
            keyElement.textContent = key;

            sectionItemElement.appendChild(keyElement);

            let valueElement = CBUI.createElement();
            valueElement.textContent = value;

            sectionItemElement.appendChild(valueElement);

            return sectionItemElement;
        }
        /* createOverviewElement_createKeyValueElement() */
    },
    /* createOverviewElement() */


    /**
     * @return Element
     */
    createShippingElement: function () {
        let element = CBUI.createElement();

        let titleElement = CBUI.createElement("CBUI_title1");
        titleElement.textContent = "Shipping";

        element.appendChild(titleElement);

        let sectionContainerElement = CBUI.createElement(
            "CBUI_sectionContainer"
        );

        element.appendChild(sectionContainerElement);

        let sectionElement = CBUI.createElement(
            "CBUI_section"
        );

        sectionContainerElement.appendChild(sectionElement);

        sectionElement.appendChild(
            createShippingElement_createKeyValueElement(
                "Name",
                CBModel.valueToString(
                    SCOrderInspector_model,
                    "shipOrderToFullName"
                )
            )
        );

        sectionElement.appendChild(
            createShippingElement_createKeyValueElement(
                "Email Address",
                CBModel.valueToString(
                    SCOrderInspector_model,
                    "shipOrderToEmail"
                )
            )
        );

        sectionElement.appendChild(
            createShippingElement_createKeyValueElement(
                "Address Line 1",
                CBModel.valueToString(
                    SCOrderInspector_model,
                    "shipOrderToAddressLine1"
                )
            )
        );

        sectionElement.appendChild(
            createShippingElement_createKeyValueElement(
                "Address Line 2",
                CBModel.valueToString(
                    SCOrderInspector_model,
                    "shipOrderToAddressLine2"
                )
            )
        );

        sectionElement.appendChild(
            createShippingElement_createKeyValueElement(
                "City",
                CBModel.valueToString(
                    SCOrderInspector_model,
                    "shipOrderToCity"
                )
            )
        );

        sectionElement.appendChild(
            createShippingElement_createKeyValueElement(
                "State / Province / Region",
                CBModel.valueToString(
                    SCOrderInspector_model,
                    "shipOrderToStateProvinceOrRegion"
                )
            )
        );

        sectionElement.appendChild(
            createShippingElement_createKeyValueElement(
                "ZIP / Postal Code",
                CBModel.valueToString(
                    SCOrderInspector_model,
                    "shipOrderToPostalCode"
                )
            )
        );

        sectionElement.appendChild(
            createShippingElement_createKeyValueElement(
                "Country",
                CBModel.valueToString(
                    SCOrderInspector_model,
                    "shipOrderToCountryName"
                )
            )
        );

        sectionElement.appendChild(
            createShippingElement_createKeyValueElement(
                "Phone",
                CBModel.valueToString(
                    SCOrderInspector_model,
                    "shipOrderToPhone"
                )
            )
        );

        sectionElement.appendChild(
            createShippingElement_createKeyValueElement(
                "Shipping Method",
                CBModel.valueToString(
                    SCOrderInspector_model,
                    "orderShippingMethod"
                )
            )
        );

        sectionElement.appendChild(
            createShippingElement_createKeyValueElement(
                "Special Instructions",
                CBModel.valueToString(
                    SCOrderInspector_model,
                    "shipOrderWithSpecialInstructions"
                )
            )
        );

        if (
            !CBModel.valueToBool(
                SCOrderInspector_model,
                "orderEmailWasSent"
            )
        ) {
            sectionElement.appendChild(
                createShippingElement_createKeyValueElement(
                    "Warning",
                    `
                    An error occurred when attempting to send an email receipt
                    for this order. Other than that, the order was completed and
                    credit card charge was authorized successfully.
                    `
                )
            );
        }

        return element;


        /* -- closures -- -- -- -- -- */

        function createShippingElement_createKeyValueElement(key, value) {
            let sectionItemElement = CBUI.createElement(
                "CBUI_container_topAndBottom"
            );

            let keyElement = CBUI.createElement("CBUI_textColor2");
            keyElement.textContent = key;

            sectionItemElement.appendChild(keyElement);

            let valueElement = CBUI.createElement();
            valueElement.textContent = value.trim() || "\u00A0";

            sectionItemElement.appendChild(valueElement);

            return sectionItemElement;
        }
        /* createShippingElement_createKeyValueElement() */
    },
    /* createShippingElement */


    /**
     * @return Element
     */
    createWholesaleElement: function () {
        let element = CBUI.createElement();

        if (
            !CBModel.valueToBool(
                SCOrderInspector_model,
                "isWholesale"
            )
        ) {
            return element;
        }

        let titleElement = CBUI.createElement("CBUI_title1");
        titleElement.textContent = "Wholesale";

        element.appendChild(titleElement);

        let sectionContainerElement = CBUI.createElement(
            "CBUI_sectionContainer"
        );

        element.appendChild(sectionContainerElement);

        let sectionElement = CBUI.createElement(
            "CBUI_section"
        );

        sectionContainerElement.appendChild(sectionElement);

        sectionElement.appendChild(
            createWholesaleElement_createKeyValueElement(
                "Company Name",
                CBModel.valueToString(
                    SCOrderInspector_wholesaleCustomerModel,
                    "companyName"
                )
            )
        );

        sectionElement.appendChild(
            createWholesaleElement_createKeyValueElement(
                "Company Tax ID Number",
                CBModel.valueToString(
                    SCOrderInspector_wholesaleCustomerModel,
                    "taxID"
                )
            )
        );

        sectionElement.appendChild(
            createWholesaleElement_createKeyValueElement(
                "Company Email",
                CBModel.valueToString(
                    SCOrderInspector_wholesaleCustomerModel,
                    "email"
                )
            )
        );

        sectionElement.appendChild(
            createWholesaleElement_createKeyValueElement(
                "Company Phone",
                CBModel.valueToString(
                    SCOrderInspector_wholesaleCustomerModel,
                    "phone"
                )
            )
        );

        let anchorElement = CBUI.createElement(
            "CBUI_container_topAndBottom",
            "a"
        );

        sectionElement.appendChild(anchorElement);

        anchorElement.href = CBUser.userIDToUserAdminPageURL(
            CBModel.valueAsID(
                SCOrderInspector_model,
                "customerHash"
            )
        );

        let textElement = CBUI.createElement("CBUI_textAlign_center");

        anchorElement.appendChild(textElement);

        textElement.textContent = "View User Page >";

        return element;


        /* -- closures -- -- -- -- -- */

        function createWholesaleElement_createKeyValueElement(key, value) {
            let sectionItemElement = CBUI.createElement(
                "CBUI_container_topAndBottom"
            );

            let keyElement = CBUI.createElement("CBUI_textColor2");
            keyElement.textContent = key;

            sectionItemElement.appendChild(keyElement);

            let valueElement = CBUI.createElement();
            valueElement.textContent = value.trim() || "\u00A0";

            sectionItemElement.appendChild(valueElement);

            return sectionItemElement;
        }
        /* createWholesaleElement_createKeyValueElement() */
    },
    /* createWholesaleElement() */


    /**
     * @return undefined
     */
    createElement: function () {
        let orderID = SCOrderInspector_model.ID;
        let element = CBUI.createElement();

        element.appendChild(
            SCOrderInspector.createOverviewElement()
        );

        element.appendChild(
            SCOrderInspector.createNotesElement()
        );

        element.appendChild(
            SCOrderInspector.createShippingElement()
        );

        element.appendChild(
            SCOrderInspector.createWholesaleElement()
        );

        {
            let orderItems = CBModel.valueToArray(
                SCOrderInspector_model,
                "orderItems"
            );

            if (orderItems.length > 0) {
                let titleElement = CBUI.createElement("CBUI_title1");
                titleElement.textContent = "Items";

                element.appendChild(titleElement);

                orderItems.forEach(
                    function (cartItemModel) {
                        element.appendChild(
                            SCCartItem.createOrderViewElement(
                                cartItemModel
                            )
                        );
                    }
                );
            }
        }

        {
            /**
             * closure
             *
             * @return undefined
             */
            let addCapturedAmountSectionItem =
            function (capturedAmountInCents) {
                let sectionItem = CBUISectionItem4.create();
                let stringsPart = CBUIStringsPart.create();
                stringsPart.string1 = "Captured Amount";

                stringsPart.string2 = CBConvert.centsToDollars(
                    capturedAmountInCents
                );

                stringsPart.element.classList.add("keyvalue");
                stringsPart.element.classList.add("sidebyside");

                sectionItem.appendPart(stringsPart);
                sectionElement.appendChild(sectionItem.element);
            };

            /* title */
            {
                let titleElement = CBUI.createElement("CBUI_title1");
                titleElement.textContent = "Payment";

                element.appendChild(titleElement);
            }
            /* title */

            let sectionElement;

            {
                let elements = CBUI.createElementTree(
                    "CBUI_sectionContainer",
                    "CBUI_section"
                );

                element.appendChild(
                    elements[0]
                );

                sectionElement = elements[1];
            }

            let paymentMethod = CBModel.valueToString(
                SCOrderInspector_model,
                "orderPaymentMethod"
            );

            {
                let sectionItem = CBUISectionItem4.create();
                let stringsPart = CBUIStringsPart.create();
                stringsPart.string1 = "Payment Method";
                stringsPart.string2 = paymentMethod;

                stringsPart.element.classList.add("keyvalue");
                stringsPart.element.classList.add("sidebyside");

                sectionItem.appendPart(stringsPart);
                sectionElement.appendChild(sectionItem.element);
            }

            if (paymentMethod === "Stripe") {
                /* charge ID */
                {
                    let chargeID = CBModel.valueToString(
                        SCOrderInspector_model,
                        "orderPaymentStripeChargeId"
                    );

                    let elements = CBUI.createElementTree(
                        "CBUI_container_sideBySide",
                        "title CBUI_textColor2 CBUI_userSelectNone"
                    );

                    sectionElement.appendChild(
                        elements[0]
                    );

                    let textContainerElement = elements[0];

                    let titleElement = elements[1];
                    titleElement.textContent = "Charge ID";

                    let valueElement = CBUI.createElement(
                        "value"
                    );

                    textContainerElement.appendChild(
                        valueElement
                    );

                    valueElement.textContent = chargeID;
                }

                let capturedAmountInCents = CBModel.valueAsInt(
                    SCOrderInspector_model,
                    "orderPaymentCapturedAmountInCents"
                );

                if (capturedAmountInCents === undefined) {
                    let authorizedAmountInCents = CBModel.valueAsInt(
                        SCOrderInspector_model,
                        "orderPaymentAuthorizedAmountInCents"
                    );

                    if (authorizedAmountInCents === undefined) {
                        let orderRowId = CBModel.valueAsInt(
                            SCOrderInspector_model,
                            "orderRowId"
                        );

                        throw Error(
                            "Order " +
                            orderRowId +
                            " does not have an authorized amount."
                        );
                    }

                    /* editor 1 */

                    let captureAmountEditor1 = CBUIStringEditor.create();
                    captureAmountEditor1.title = "Capture Amount";
                    captureAmountEditor1.value = CBConvert.centsToDollars(
                        authorizedAmountInCents
                    );

                    sectionElement.appendChild(captureAmountEditor1.element);

                    /* editor 2 */

                    let captureAmountEditor2 = CBUIStringEditor.create();
                    captureAmountEditor2.title = "Re-enter Capture Amount";

                    sectionElement.appendChild(captureAmountEditor2.element);

                    /* manual capture */

                    let manualCaptureSectionItemElement =
                    SCOrderInspector.
                    createManualCaptureSectionItemElement();

                    sectionElement.appendChild(
                        manualCaptureSectionItemElement
                    );

                    /* capture button */

                    let captureButtonSectionItem = CBUISectionItem4.create();
                    let captureButtonStringsPart = CBUIStringsPart.create();
                    captureButtonStringsPart.string1 = "Capture";
                    captureButtonStringsPart.element.classList.add("action");

                    captureButtonSectionItem.appendPart(
                        captureButtonStringsPart
                    );

                    sectionElement.appendChild(
                        captureButtonSectionItem.element
                    );

                    let isCapturing = false;

                    /**
                     * closure
                     *
                     * @return undefined
                     */
                    captureButtonSectionItem.callback = function() {
                        if (isCapturing) {
                            return;
                        }

                        let amountInCents1 = CBConvert.dollarsAsCents(
                            captureAmountEditor1.value
                        );

                        if (amountInCents1 === undefined) {
                            CBUIPanel.displayCBMessage(`

                                The "Capture Amount" value is not a valid dollar
                                amount.

                            `);

                            return;
                        }

                        let amountInCents2 = CBConvert.dollarsAsCents(
                            captureAmountEditor2.value
                        );

                        if (amountInCents2 === undefined) {
                            CBUIPanel.displayCBMessage(`

                                The "Re-enter Capture Amount" value is not a
                                valid dollar amount.

                            `);

                            return;
                        }

                        if (amountInCents1 !== amountInCents2) {
                            CBUIPanel.displayCBMessage(`

                                The "Capture Amount" value and the "Re-enter
                                Capture Amount" value must be the same amount.

                            `);

                            return;
                        }

                        if (amountInCents1 > authorizedAmountInCents) {
                            let authorizedAmountInDollars = (
                                CBConvert.centsToDollars(
                                    authorizedAmountInCents
                                )
                            );

                            CBUIPanel.displayCBMessage(`

                                The capture amount cannot be greater than the
                                authorized amount of
                                ${authorizedAmountInDollars}

                            `);

                            return;
                        }

                        isCapturing = true;

                        captureButtonStringsPart.string1 = "capturing...";
                        captureButtonStringsPart.element.classList.add(
                            'disabled'
                        );

                        Colby.callAjaxFunction(
                            "SCOrder",
                            "capture",
                            {
                                orderID: orderID,
                                amountInCents: amountInCents1,
                                captureWasManual: (
                                    manualCaptureSectionItemElement.
                                    captureWasManual
                                ),
                            }
                        ).then(
                            function () {
                                sectionElement.removeChild(
                                    captureAmountEditor1.element
                                );

                                sectionElement.removeChild(
                                    captureAmountEditor2.element
                                );

                                sectionElement.removeChild(
                                    captureButtonSectionItem.element
                                );

                                sectionElement.removeChild(
                                    manualCaptureSectionItemElement
                                );

                                addCapturedAmountSectionItem(
                                    amountInCents1
                                );
                            }
                        ).catch(
                            function (error) {
                                CBUIPanel.displayAndReportError(error);
                            }
                        );
                    };
                    /* captureButtonSectionItem.callback */

                } else {
                    addCapturedAmountSectionItem(capturedAmountInCents);
                }
                /* capturedAmountInCents === undefined */

            }
            /* paymentMethod === "Stripe" */
        }

        {
            let sectionItemElement;

            {
                let elements = CBUI.createElementTree(
                    "CBUI_sectionContainer",
                    "CBUI_section",
                    "CBUI_sectionItem",
                    "CBUI_container_topAndBottom CBUI_flexGrow",
                    "title"
                );

                element.appendChild(
                    elements[0]
                );

                sectionItemElement = elements[2];

                let titleElement = elements[4];
                titleElement.textContent = "Is Archived";
            }

            let changeInProgress = false;
            let switchPart = CBUIBooleanSwitchPart.create();

            sectionItemElement.appendChild(
                switchPart.element
            );

            switchPart.value = SCOrderInspector_originalIsArchived;

            switchPart.changed = function () {
                if (changeInProgress) {
                    return;
                }

                changeInProgress = true;

                Colby.callAjaxFunction(
                    "SCOrderInspector",
                    "setIsArchived",
                    {
                        "isArchived": switchPart.value,
                        "orderID": SCOrderInspector_orderID,
                    }
                ).then(
                    function () {
                        changeInProgress = false;
                    }
                ).catch(
                    function (error) {
                        switchPart.value = !switchPart.value;
                        changeInProgress = false;

                        CBUIPanel.displayAndReportError(error);
                    }
                );
            };
        }


        /* resend email */
        {
            let elements = CBUI.createElementTree(
                "CBUI_container1",
                "CBUI_button1"
            );

            element.appendChild(
                elements[0]
            );

            let buttonElement = elements[1];
            buttonElement.textContent = "Resend Email";

            buttonElement.addEventListener(
                "click",
                function () {
                    Colby.callAjaxFunction(
                        "SCOrder",
                        "sendEmail",
                        {
                            orderID: SCOrderInspector_orderID,
                        }
                    ).then(
                        function () {
                            CBUIPanel.displayCBMessage(`

                                    The email was sent.

                            `);
                        }
                    ).catch(
                        function (error) {
                            return CBUIPanel.displayAndReportError(error);
                        }
                    );
                }
            );
        }
        /* resend email */


        /* developers */
        if (SCOrderInspector_userIsADeveloper) {

            /* inspect SCOrder model */
            {
                let elements = CBUI.createElementTree(
                    "CBUI_sectionContainer",
                    "CBUI_section",
                    ["CBUI_action", "a"]
                );

                element.appendChild(
                    elements[0]
                );

                let actionElement = elements[2];

                actionElement.textContent = "Inspect SCOrder Model >";

                actionElement.href = (
                    "/admin/?c=CBModelInspector&ID=" +
                    SCOrderInspector_orderID
                );
            }
            /* inspect SCOrder model */


            /* HTML email */
            {
                let expander = CBUIExpander.create();
                expander.title = "Email HTML";

                let contentElement = document.createElement("div");
                contentElement.className = "SCOrderInspector_emailPreview";

                let iframe = document.createElement("iframe");
                iframe.srcdoc = SCOrderInspector_emailHTML;

                contentElement.appendChild(iframe);

                expander.contentElement = contentElement;

                element.appendChild(expander.element);
            }
            /* HTML email */


            /* Text email */
            {
                let message = CBMessageMarkup.stringToMessage(
                    SCOrderInspector_emailText
                );

                message = `

                    --- pre\n${message}
                    ---

                `;

                let expander = CBUIExpander.create();
                expander.title = "Email Text";
                expander.message = message;

                element.appendChild(expander.element);
            }
            /* Text email */
        }
        /* developers */


        return element;
    },
    /* createElement() */

};
/* SCOrderInspector */



Colby.afterDOMContentLoaded(
    function SCOrderInspector_afterDOMContentLoaded() {
        let main = document.getElementsByTagName("main")[0];
        let navigationView = CBUINavigationView.create();

        main.appendChild(navigationView.element);

        CBUINavigationView.navigate(
            {
                element: SCOrderInspector.createElement(),
                title: (
                    "Order " +
                    CBModel.valueAsInt(
                        SCOrderInspector_model,
                        "orderRowId"
                    ) || 0
                ),
            }
        );
    }
    /* SCOrderInspector_afterDOMContentLoaded */
);
