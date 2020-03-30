"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported SCPromotionEditor */
/* global
    CBConvert,
    CBErrorHandler,
    CBModel,
    CBUI,
    CBUIPanel,
    CBUIStringEditor,
    CBUISpecEditor,
    CBUIUnixTimestampEditor,
    Colby,
*/


(function () {

    window.SCPromotionEditor = {
        CBUISpecEditor_createEditorElement,
    };



    /**
     * @param object args
     *
     *      {
     *          spec: object
     *          specChangedCallback: function
     *      }
     *
     * @return Element
     */
    function CBUISpecEditor_createEditorElement(
        args
    ) {
        let spec = args.spec;
        let specChangedCallback = args.specChangedCallback;

        let elements = CBUI.createElementTree(
            "SCPromotionEditor",
            "CBUI_sectionContainer",
            "CBUI_section"
        );

        let element = elements[0];
        let sectionElement = elements[2];

        {
            let titleEditor = CBUIStringEditor.create();
            titleEditor.title = "Title";

            titleEditor.value = CBModel.valueToString(
                spec,
                "title"
            );

            titleEditor.changed = function () {
                spec.title = titleEditor.value;
                specChangedCallback();
            };

            sectionElement.appendChild(
                titleEditor.element
            );
        }


        {
            let beginTimestampEditor = CBUIUnixTimestampEditor.create(
                {
                    labelText: "Begin Time",
                    propertyName: "beginTimestamp",
                    spec,
                    specChangedCallback,
                }
            );

            sectionElement.appendChild(
                beginTimestampEditor.element
            );
        }


        {
            let endTimestampEditor = CBUIUnixTimestampEditor.create(
                {
                    labelText: "End Time",
                    propertyName: "endTimestamp",
                    spec,
                    specChangedCallback,
                }
            );

            sectionElement.appendChild(
                endTimestampEditor.element
            );
        }


        {
            let executorSpec = CBModel.valueAsModel(
                spec,
                "executor"
            );

            if (executorSpec !== undefined) {
                let executorEditor = CBUISpecEditor.create(
                    {
                        spec: executorSpec,
                        specChangedCallback,
                        useStrict: true,
                    }
                );

                element.appendChild(
                    executorEditor.element
                );
            }
        }


        {
            let elements = CBUI.createElementTree(
                "CBUI_container1",
                "CBUI_button1"
            );

            let buttonElement = elements[1];
            buttonElement.textContent = "Delete Promotion";

            buttonElement.addEventListener(
                "click",
                function () {
                    CBUIPanel.confirmText(
                        "Are you sure you want to delete this promotion?"
                    ).then(
                        function (didConfirm) {
                            if (didConfirm) {
                                return Colby.callAjaxFunction(
                                    "CBModels",
                                    "deleteByID",
                                    {
                                        ID: spec.ID,
                                    }
                                ).then(
                                    function () {
                                        return CBUIPanel.displayText(
                                            CBConvert.stringToCleanLine(`

                                                The promotion was deleted, you will now be
                                                returned to the promotions admin page.

                                            `)
                                        );
                                    }
                                ).then(
                                    function () {
                                        window.location.href = (
                                            "/admin/?c=SCPromotionsAdmin"
                                        );
                                    }
                                );
                            }
                        }
                    ).catch(
                        function (error) {
                            CBErrorHandler.displayAndReport(error);
                        }
                    );
                }
            );

            element.appendChild(
                elements[0]
            );
        }

        return element;
    }
    /* CBUISpecEditor_createEditorElement() */

})();
