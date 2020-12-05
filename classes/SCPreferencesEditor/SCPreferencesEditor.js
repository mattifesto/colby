"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported SCPreferencesEditor */
/* global
    CBAjax,
    CBException,
    CBModel,
    CBUI,
    CBUIPanel,
    CBUIStringEditor,
    Colby,

    SCPreferencesEditor_defaultOrderKindClassName,
*/

var SCPreferencesEditor = {

    /**
     * @param args object
     *
     *      {
     *          spec: object
     *          specChangedCallback: function
     *      }
     *
     * @return Element
     */
    CBUISpecEditor_createEditorElement: function (args) {
        let spec = CBModel.valueAsObject(
            args,
            "spec"
        );

        if (spec === undefined) {
            throw CBException.withValueRelatedError(
                TypeError(
                    "The \"spec\" property is not an object."
                ),
                args,
                "3084f59891baf07191495e5b225b57a009208f8f"
            );
        }

        let callback = args.specChangedCallback;

        if (typeof callback !== "function") {
            callback = function () {};
        }

        let element = CBUI.createElement(
            "SCStripePreferencesEditor"
        );

        /* email section */
        {
            let sectionContainerElement = CBUI.createElement(
                "CBUI_sectionContainer"
            );

            element.appendChild(
                sectionContainerElement
            );

            let sectionElement = CBUI.createElement(
                "CBUI_section"
            );

            sectionContainerElement.appendChild(
                sectionElement
            );

            {
                let stringEditor = CBUIStringEditor.create();
                stringEditor.title = "SMTP Server Hostname";

                stringEditor.value = CBModel.valueToString(
                    spec,
                    "SMTPServerHostname"
                );

                stringEditor.changed = function () {
                    spec.SMTPServerHostname = stringEditor.value;
                    callback();
                };

                sectionElement.appendChild(
                    stringEditor.element
                );
            }

            {
                let stringEditor = CBUIStringEditor.create();
                stringEditor.title = "SMTP Server Port";

                stringEditor.value = CBModel.valueToString(
                    spec,
                    "SMTPServerPort"
                );

                stringEditor.changed = function () {
                    spec.SMTPServerPort = stringEditor.value;
                    callback();
                };

                sectionElement.appendChild(
                    stringEditor.element
                );
            }

            {
                let stringEditor = CBUIStringEditor.create();
                stringEditor.title = "SMTP Server Security";

                stringEditor.value = CBModel.valueToString(
                    spec,
                    "SMTPServerSecurity"
                );

                stringEditor.changed = function () {
                    spec.SMTPServerSecurity = stringEditor.value;
                    callback();
                };

                sectionElement.appendChild(
                    stringEditor.element
                );
            }

            {
                let stringEditor = CBUIStringEditor.create();
                stringEditor.title = "SMTP Server Username";

                stringEditor.value = CBModel.valueToString(
                    spec,
                    "SMTPServerUsername"
                );

                stringEditor.changed = function () {
                    spec.SMTPServerUsername = stringEditor.value;
                    callback();
                };

                sectionElement.appendChild(
                    stringEditor.element
                );
            }

            {
                let stringEditor = CBUIStringEditor.create();
                stringEditor.title = "SMTP Server Password";

                stringEditor.value = CBModel.valueToString(
                    spec,
                    "SMTPServerPassword"
                );

                stringEditor.changed = function () {
                    spec.SMTPServerPassword = stringEditor.value;
                    callback();
                };

                sectionElement.appendChild(
                    stringEditor.element
                );
            }

            {
                let stringEditor = CBUIStringEditor.create();
                stringEditor.title = "Sending Email Address";

                stringEditor.value = CBModel.valueToString(
                    spec,
                    "sendingEmailAddress"
                );

                stringEditor.changed = function () {
                    spec.sendingEmailAddress = stringEditor.value;
                    callback();
                };

                sectionElement.appendChild(
                    stringEditor.element
                );
            }

            {
                let stringEditor = CBUIStringEditor.create();
                stringEditor.title = "Sending Email Name";

                stringEditor.value = CBModel.valueToString(
                    spec,
                    "sendingEmailName"
                );

                stringEditor.changed = function () {
                    spec.sendingEmailName = stringEditor.value;
                    callback();
                };

                sectionElement.appendChild(
                    stringEditor.element
                );
            }
        }
        /* email section */

        element.appendChild(
            CBUISpecEditor_createEditorElement_createOrderKindSectionElement()
        );

        return element;


        /* -- closures -- -- -- -- -- */

        /**
         * @return Element
         */
        function CBUISpecEditor_createEditorElement_createOrderKindSectionElement() {
            let sectionContainerElement = CBUI.createElement(
                "CBUI_sectionContainer"
            );

            let sectionElement = CBUI.createElement(
                "CBUI_section"
            );

            sectionContainerElement.appendChild(
                sectionElement
            );

            if (SCPreferencesEditor_defaultOrderKindClassName === "") {
                let actionElement = CBUI.createElement(
                    "CBUI_action"
                );

                actionElement.textContent = "Generate Default Order Kind Class";

                actionElement.addEventListener(
                    "click",
                    closure_click
                );

                sectionElement.appendChild(
                    actionElement
                );
            } else {
                let textContainerElement = CBUI.createElement(
                    "CBUI_container_topAndBottom"
                );

                sectionElement.appendChild(
                    textContainerElement
                );

                let textElement = CBUI.createElement();

                textElement.textContent =
                SCPreferencesEditor_defaultOrderKindClassName;

                textContainerElement.appendChild(
                    textElement
                );
            }

            return sectionContainerElement;


            /* -- closures -- -- -- -- -- */

            /**
             * @return undefined
             */
            function closure_click() {
                let orderKindClassName = "OrderKind";
                let element = CBUI.createElement();

                let sectionContainerElement = CBUI.createElement(
                    "CBUI_sectionContainer CBUI_padding_half"
                );

                element.appendChild(sectionContainerElement);

                let sectionElement = CBUI.createElement(
                    "CBUI_section CBUI_section_inner"
                );

                sectionContainerElement.appendChild(sectionElement);

                let stringEditor = CBUIStringEditor.create();
                stringEditor.title = "Order Kind Class Name";
                stringEditor.value = orderKindClassName;

                stringEditor.changed = function () {
                    orderKindClassName = stringEditor.value;
                };

                sectionElement.appendChild(
                    stringEditor.element
                );

                let createActionElement = CBUI.createElement(
                    "CBUI_action"
                );

                createActionElement.textContent = "Generate";

                createActionElement.addEventListener(
                    "click",
                    function () {
                        closure_generate(orderKindClassName);
                    }
                );

                sectionElement.appendChild(createActionElement);

                let cancelActionElement = CBUI.createElement(
                    "CBUI_action"
                );

                cancelActionElement.textContent = "Cancel";

                cancelActionElement.addEventListener(
                    "click",
                    function () {
                        CBUIPanel.hidePanelWithContentElement(
                            element
                        );
                    }
                );

                sectionElement.appendChild(cancelActionElement);

                CBUIPanel.displayElement(element);
            }
            /* closure_click() */


            /**
             * @param string orderKindClassName
             *
             * @return undefined
             */
            function closure_generate(orderKindClassName) {
                CBAjax.call(
                    "SCOrderKind",
                    "generateDefaultOrderKindClass",
                    {
                        orderKindClassName: orderKindClassName,
                    }
                ).then(
                    function () {
                        window.location.reload();
                    }
                ).catch(
                    function (error) {
                        CBUIPanel.displayError(error);
                        Colby.reportError(error);
                    }
                );
            }
            /* closure_generate() */
        }
        /* CBUISpecEditor_createEditorElement_createOrderKindSectionElement() */
    },
    /* CBUISpecEditor_createEditorElement() */
};
