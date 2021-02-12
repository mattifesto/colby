/* global
    SCShippingAddressEditorView,

    CBEvent,
    CBModel,
    CBUI,
    CBUIMessagePart,
    CBUINavigationView,
    CBUISectionItem4,
    CBUISelector,
    CBUIStringEditor,
    CBUIStringsPart,
    Colby,
    SCShippingAddress,

    SCShippingAddressEditorView_countryOptions,
*/



(function () {
    "use strict";

    Colby.afterDOMContentLoaded(
        afterDOMContentLoaded
    );



    let emailAddressEditor;
    let confirmEmailAddressEditor;
    let countryEditor;

    let invalidFields = {};


    /**
     * @return undefined
     */
    function afterDOMContentLoaded() {
        let shippingAddressSpec = SCShippingAddress.fetchLocalSpec();

        let countryCBID = SCShippingAddress.getCountryCBID(
            shippingAddressSpec
        );

        if (countryCBID === undefined) {
            SCShippingAddress.setCountryCBID(
                shippingAddressSpec,
                SCShippingAddressEditorView.getDefaultCountryCBID()
            );
        }

        SCShippingAddressEditorView.model = shippingAddressSpec;

        SCShippingAddressEditorView.display();
    }
    /* afterDOMContentLoaded() */



    window.SCShippingAddressEditorView = {

        /**
         * This property holds the message part used to display information
         * about which fields need to be updated for the address to be valid.
         */
        invalidFieldsMessagePart: CBUIMessagePart.create(),

        /**
         * This property will be initialialized to an object value by init().
         */
        model: undefined,



        /**
         * @return undefined
         */
        display() {
            let viewElement;

            {
                let viewElements = document.getElementsByClassName(
                    "SCShippingAddressEditorView"
                );

                if (viewElements.length === 0) {
                    return;
                }

                viewElement = viewElements[0];
            }

            let model = SCShippingAddressEditorView.model;
            let modelChangedOutsideOfEditorEvent = CBEvent.create();
            let navigationView = CBUINavigationView.create();

            viewElement.appendChild(navigationView.element);

            let element = document.createElement("div");

            navigationView.navigate(
                {
                    element: element,
                    title: "Order Information",
                }
            );


            {
                let titleElement = CBUI.createElement(
                    "CBUI_title1"
                );

                element.appendChild(
                    titleElement
                );

                titleElement.textContent = "Shipping Address";
            }


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


            /* full-name */

            sectionElement.appendChild(
                createFullNameEditorElement()
            );


            /* email address */

            emailAddressEditor = CBUIStringEditor.create();

            sectionElement.appendChild(
                emailAddressEditor.element
            );

            emailAddressEditor.title = "Email Address";

            emailAddressEditor.value = CBModel.valueToString(
                SCShippingAddressEditorView.model,
                "email-address"
            );

            emailAddressEditor.changed = function () {
                valueForEmailAddressWasUpdated();
            };

            modelChangedOutsideOfEditorEvent.addListener(
                function () {
                    emailAddressEditor.value = CBModel.valueToString(
                        SCShippingAddressEditorView.model,
                        "email-address"
                    );
                }
            );


            /* confirm email address */

            confirmEmailAddressEditor = CBUIStringEditor.create();

            sectionElement.appendChild(
                confirmEmailAddressEditor.element
            );

            confirmEmailAddressEditor.title = "Confirm Email Address";
            confirmEmailAddressEditor.value = emailAddressEditor.value;
            confirmEmailAddressEditor.changed = emailAddressEditor.changed;

            modelChangedOutsideOfEditorEvent.addListener(
                function () {
                    confirmEmailAddressEditor.value = CBModel.valueToString(
                        SCShippingAddressEditorView.model,
                        "email-address"
                    );
                }
            );


            /* --- */

            sectionElement.appendChild(
                createEditorSectionItem_addressLine1().element
            );

            sectionElement.appendChild(
                createEditorSectionItem_addressLine2().element
            );

            sectionElement.appendChild(
                createEditorSectionItem_city().element
            );

            sectionElement.appendChild(
                createEditorSectionItem_state().element
            );

            sectionElement.appendChild(
                createEditorSectionItem_zip().element
            );

            sectionElement.appendChild(
                createEditorSectionItem_country().element
            );

            sectionElement.appendChild(
                createEditorSectionItem_phone().element
            );

            sectionElement.appendChild(
                createEditorSectionItem_specialInstructions().element
            );


            {
                let elements = CBUI.createElementTree(
                    "CBUI_sectionContainer",
                    "CBUI_section"
                );

                element.appendChild(
                    elements[0]
                );

                let sectionElement = elements[1];

                {
                    let sectionItem = CBUISectionItem4.create();

                    sectionItem.appendPart(
                        SCShippingAddressEditorView.invalidFieldsMessagePart
                    );

                    sectionElement.appendChild(sectionItem.element);
                }

                {
                    let sectionItem = CBUISectionItem4.create();

                    sectionItem.callback = function () {
                        if (SCShippingAddressEditorView.isComplete) {
                            location.href = "/checkout/900/";
                        } else {
                            window.alert(
                                "The shipping address is not complete."
                            );
                        }
                    };

                    let stringsPart = CBUIStringsPart.create();
                    stringsPart.string1 = "Ship to this Address and Continue";

                    stringsPart.element.classList.add("action");

                    sectionItem.appendPart(stringsPart);
                    sectionElement.appendChild(sectionItem.element);
                }

                {
                    let sectionItem = CBUISectionItem4.create();

                    sectionItem.callback = function() {
                        if (
                            window.confirm(
                                "Are you sure you want to clear the shipping " +
                                "address form?"
                            )
                        ) {
                            let newShippingAddressSpec = {};

                            CBModel.setClassName(
                                newShippingAddressSpec,
                                "SCShippingAddress"
                            );

                            SCShippingAddress.setCountryCBID(
                                newShippingAddressSpec,
                                SCShippingAddressEditorView.getDefaultCountryCBID()
                            );

                            model =
                            SCShippingAddressEditorView.model =
                            newShippingAddressSpec;

                            valuesForAllFieldsWereUpdated();
                            SCShippingAddressEditorView.modelWasUpdated();
                        }
                    };

                    let stringsPart = CBUIStringsPart.create();
                    stringsPart.string1 = "Clear Form";

                    stringsPart.element.classList.add("action");

                    sectionItem.appendPart(stringsPart);
                    sectionElement.appendChild(sectionItem.element);
                }
            }

            valuesForAllFieldsWereUpdated();

            return;



            /* -- closures -- -- -- -- -- */



            /**
             * @param object args
             *
             *      {
             *          title: string
             *          propertyName: string
             *          isValueValidCallback: function
             *      }
             *
             * @return object (CBUIStringEditor)
             */
            function createStringEditor(
                args
            ) {
                let isValueValidCallback = args.isValueValidCallback;

                if (typeof isValueValidCallback !== "function") {
                    isValueValidCallback = function () { return true; };
                }

                let propertyName = args.propertyName;
                let title = args.title;

                let stringEditor = CBUIStringEditor.create();
                stringEditor.title = title;
                stringEditor.changed = editorValueChangedByUser;

                modelChangedOutsideOfEditorEvent.addListener(
                    transferModelValueToEditor
                );

                return stringEditor;



                /* -- closures -- -- -- -- -- */



                /**
                 * @closure in createStringEditor() in display()
                 *
                 * @return undefined
                 */
                function editorValueBecameInvalid() {
                    stringEditor.element.classList.add(
                        "SCShippingAddressEditorView_invalid"
                    );

                    invalidFields[propertyName] = title;

                    updateMessageForInvalidFields();
                }
                /* editorValueBecameInvalid() */



                /**
                 * @closure in createStringEditor() in display()
                 *
                 * @return undefined
                 */
                function editorValueBecameValid() {
                    stringEditor.element.classList.remove(
                        "SCShippingAddressEditorView_invalid"
                    );

                    delete invalidFields[propertyName];

                    updateMessageForInvalidFields();
                }
                /* editorValueBecameValid() */



                /**
                 * @closure in createStringEditor() in display()
                 *
                 * @return undefined
                 */
                function editorValueChangedByUser() {
                    let isValueValid = isValueValidCallback(
                        stringEditor.value
                    );

                    if (isValueValid) {
                        transferEditorValueToModel();
                        editorValueBecameValid();
                    } else {
                        editorValueBecameInvalid();
                    }
                }
                /* editorValueChangedByUser() */



                /**
                 * @closure in createStringEditor() in display()
                 *
                 * @return undefined
                 */
                function transferEditorValueToModel() {
                    model[propertyName] = stringEditor.value;

                    SCShippingAddressEditorView.modelWasUpdated();
                }



                /**
                 * @closure in createStringEditor() in display()
                 *
                 * @return undefined
                 */
                function transferModelValueToEditor() {
                    stringEditor.value = CBModel.valueToString(
                        model,
                        propertyName
                    );

                    let isValueValid = isValueValidCallback(
                        stringEditor.value
                    );

                    if (isValueValid) {
                        editorValueBecameValid();
                    } else {
                        editorValueBecameInvalid();
                    }
                }
                /* transferModelValueToEditor() */

            }
            /* createStringEditor() */



            /**
             * @closure in display()
             *
             * @return object (CBUISectionItem4)
             */
            function createEditorSectionItem_addressLine1() {
                return createStringEditor(
                    {
                        title: "Address Line 1",
                        propertyName: "address-line-1",
                        isValueValidCallback: valueContainsALetterOrNumber,
                    }
                );
            }



            /**
             * @closure in display()
             *
             * @return object (CBUISectionItem4)
             */
            function createEditorSectionItem_addressLine2() {
                return createStringEditor(
                    {
                        title: "Address Line 2",
                        propertyName: "address-line-2",
                    }
                );
            }



            /**
             * @closure in display()
             *
             * @return object (CBUISectionItem4)
             */
            function createEditorSectionItem_city() {
                return createStringEditor(
                    {
                        title: "City",
                        propertyName: "city",
                        isValueValidCallback: valueContainsALetter,
                    }
                );
            }



            /**
             * @closure in display()
             *
             * @return object (CBUISectionItem4)
             */
            function createEditorSectionItem_country() {
                countryEditor = CBUISelector.create();
                countryEditor.title = "Country";

                countryEditor.options = (
                    SCShippingAddressEditorView_countryOptions
                );

                countryEditor.changed = function () {
                    valueForCountryWasUpdated();
                };

                modelChangedOutsideOfEditorEvent.addListener(
                    transferModelCountryValueToCountryEditor
                );

                transferModelCountryValueToCountryEditor();

                return countryEditor;



                /**
                 * @closure in createEditorSectionItem_country() in display()
                 */
                function transferModelCountryValueToCountryEditor() {
                    countryEditor.value = (
                        SCShippingAddressEditorView.model.country
                    );
                }

            }
            /* createEditorSectionItem_country() */



            /**
             * @closure in display()
             *
             * @return Element
             */
            function createFullNameEditorElement() {
                return createStringEditor(
                    {
                        title: "Full Name",
                        propertyName: "full-name",
                        isValueValidCallback: valueContainsALetter,
                    }
                ).element;
            }



            /**
             * @closure in display()
             *
             * @return object (CBUISectionItem4)
             */
            function createEditorSectionItem_phone() {
                return createStringEditor(
                    {
                        title: "Phone",
                        propertyName: "phone",
                        isValueValidCallback: valueContainsAPhoneNumberOrIsEmpty,
                    }
                );
            }



            /**
             * @closure in display()
             *
             * @return object (CBUISectionItem4)
             */
            function createEditorSectionItem_specialInstructions() {
                return createStringEditor(
                    {
                        title: "Special Instructions",
                        propertyName: "special-instructions",
                    }
                );
            }



            /**
             * @closure in display()
             *
             * @return object (CBUISectionItem4)
             */
            function createEditorSectionItem_state() {
                return createStringEditor(
                    {
                        title: "State / Province / Region",
                        propertyName: "state-province-or-region",
                    }
                );
            }



            /**
             * @closure in display()
             *
             * @return object (CBUISectionItem4)
             */
            function createEditorSectionItem_zip() {
                return createStringEditor(
                    {
                        title: "ZIP / Postal Code",
                        propertyName: "zip",
                    }
                );
            }


            /**
             * @closure in display()
             *
             * @return bool
             */
            function valueContainsALetter(value) {
                return /[a-zA-Z]/.test(value);
            }



            /**
             * @closure in display()
             *
             * @return bool
             */
            function valueContainsALetterOrNumber(value) {
                return /[a-zA-Z0-9]/.test(value);
            }



            /**
             * @closure in display()
             *
             * @return bool
             */
            function valueContainsAPhoneNumberOrIsEmpty(value) {
                return (
                    value.trim() === "" ||
                    /[0-9]{3}[^0-9]*[0-9]{3}[^0-9]*[0-9]{4}/.test(value)
                );
            }



            /**
             * @closure in display()
             *
             * @return bool
             *
             * function valueContainsFiveConsecutiveDigits(value) {
             *     return /[0-9]{5}/.test(value);
             * }
             */

            /**
             * @closure in display()
             *
             * @return bool
             *
             * function valueContainsTwoConsecutiveLetters(value) {
             *     return /[a-zA-Z]{2}/.test(value);
             * }
             */

            /**
             * @closure in display()
             *
             * This function should only be called when many changes have been
             * made to the form field values, such as when we initialize or
             * clear the form, and most of the form field values have probably
             * been updated.
             *
             * @return undefined
             */
            function valuesForAllFieldsWereUpdated() {
                modelChangedOutsideOfEditorEvent.dispatch();

                valueForEmailAddressWasUpdated();
                valueForCountryWasUpdated();
            }

        },
        /* display() */



        /**
         * @return CBID
         */
        getDefaultCountryCBID() {
            let defaultCountryOption = (
                SCShippingAddressEditorView_countryOptions.find(
                    function (option) {
                        return CBModel.valueToBool(
                            option,
                            "isDefault"
                        );
                    }
                )
            );

            if (typeof defaultCountryOption === "object") {
                return defaultCountryOption.value;
            } else {
                return undefined;
            }
        },
        /* getDefaultCountryCBID() */



        /**
         * @return undefined
         */
        modelWasUpdated(
        ) {
            SCShippingAddress.saveLocalSpec(
                SCShippingAddressEditorView.model
            );
        },
        /* modelWasUpdated() */

    };
    /*  SCShippingAddressEditorView */



    /**
     * @return undefined
     */
    function updateMessageForInvalidFields() {
        let keys = Object.keys(invalidFields);

        if (keys.length > 0) {
            let fieldNames = keys.map(
                function (key) {
                    return invalidFields[key];
                }
            );

            fieldNames = fieldNames.join("((br))");

            let message = (
                "--- div center\n\n" +
                "The following fields have missing or invalid values:\n\n" +
                fieldNames +
                "\n\n" +
                "---"
            );

            SCShippingAddressEditorView.invalidFieldsMessagePart.message = (
                message
            );

            SCShippingAddressEditorView.isComplete = false;
        } else {
            SCShippingAddressEditorView.invalidFieldsMessagePart.message = (
                "--- p center\nThe shipping information is valid.\n---"
            );

            SCShippingAddressEditorView.isComplete = true;
        }
    }
    /* updateMessageForInvalidFields() */



    /**
     * @return void
     */
    function valueForCountryWasUpdated() {
        let value = countryEditor.value;

        let allowedCountryCodes = (
            SCShippingAddressEditorView_countryOptions.map(
                function (option) {
                    return option.value;
                }
            )
        );

        if (allowedCountryCodes.includes(value)) {
            countryEditor.element.classList.remove(
                "SCShippingAddressEditorView_invalid"
            );

            delete invalidFields.country;
        } else {
            countryEditor.element.classList.add(
                "SCShippingAddressEditorView_invalid"
            );

            invalidFields.country = "Country";
        }

        updateMessageForInvalidFields();

        if (value != SCShippingAddressEditorView.model.country) {
            SCShippingAddressEditorView.model.country = value;

            SCShippingAddressEditorView.modelWasUpdated();
        }
    }
    /* valueForCountryWasUpdated() */



    /**
     * @return undefined
     */
    function valueForEmailAddressWasUpdated() {
        var validEmailAddressExpression = /\S@\S/;
        let emailAddress = emailAddressEditor.value.trim();
        let emailAddressIsValid = false;
        let confirmEmailAddress = confirmEmailAddressEditor.value.trim();
        let confirmEmailAddressIsValid = false;

        if (validEmailAddressExpression.test(emailAddress)) {
            emailAddressEditor.element.classList.remove(
                "SCShippingAddressEditorView_invalid"
            );

            delete invalidFields.emailAddress;

            emailAddressIsValid = true;
        } else {
            emailAddressEditor.element.classList.add(
                "SCShippingAddressEditorView_invalid"
            );

            invalidFields.emailAddress = (
                "Email Address"
            );
        }

        if (validEmailAddressExpression.test(confirmEmailAddress)) {
            confirmEmailAddressEditor.element.classList.remove(
                "SCShippingAddressEditorView_invalid"
            );

            delete invalidFields.confirmEmailAddress;

            confirmEmailAddressIsValid = true;
        } else {
            confirmEmailAddressEditor.element.classList.add(
                "SCShippingAddressEditorView_invalid"
            );

            invalidFields.confirmEmailAddress = (
                "Confirm Email Address"
            );
        }

        let currentEmailAddress = (
            SCShippingAddressEditorView.model["email-address"]
        );

        let calculatedEmailAddress;

        /**
         * If both email addresses are valid and the email addresses match, set
         * calculatedEmailAddress to emailAddress. If not, leave
         * calculatedEmailAddress undefined and make the confirmation email
         * address invalid.
         */
        if (
            emailAddressIsValid &&
            confirmEmailAddressIsValid &&
            emailAddress === confirmEmailAddress
        ) {
            calculatedEmailAddress = emailAddress;
        } else {
            confirmEmailAddressEditor.element.classList.add(
                "SCShippingAddressEditorView_invalid"
            );

            invalidFields.confirmEmailAddress = (
                "Confirm Email Address"
            );
        }

        if (calculatedEmailAddress !== currentEmailAddress) {
            SCShippingAddressEditorView.model["email-address"] = (
                calculatedEmailAddress
            );

            SCShippingAddressEditorView.modelWasUpdated();
        }

        updateMessageForInvalidFields();
    }
    /* valueForEmailAddressWasUpdated() */

})();
