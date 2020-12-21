"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBPageLayoutEditor */
/* global
    CBModel,
    CBUI,
    CBUIBooleanEditor,
    CBUIStringEditor2,
*/

var CBPageLayoutEditor = {

    /**
     * @param object args
     *
     *      {
     *          CBUISpecEditor_getSpec() -> object
     *          CBUISpecEditor_getSpecChangedCallback() -> function
     *      }
     *
     * @return Element
     */
    CBUISpecEditor_createEditorElement: function (
        args
    ) {
        let spec = args.CBUISpecEditor_getSpec();
        let specChangedCallback = args.CBUISpecEditor_getSpecChangedCallback();

        let element = CBUI.createElement(
            "CBPageLayoutEditor"
        );

        element.appendChild(
            CBUI.createHalfSpace()
        );

        element.appendChild(
            CBUI.createSectionHeader(
                {
                    paragraphs: [
                        `CSS class names can provide additional options. CSS
                         class names commonly used with this view are:`,
                        `CBLightTheme: a theme with a light background and dark
                         text.`,
                        `CBDarkTheme: a theme with a dark backround and light
                         text.`,
                        `endContentWithWhiteSpace: adds a comfortable amount of
                         white space and the end of the page content.`,
                    ],
                }
            )
        );


        /* CSS class names */
        {
            let elements = CBUI.createElementTree(
                "CBUI_sectionContainer",
                "CBUI_section"
            );

            element.appendChild(
                elements[0]
            );

            let sectionElement = elements[1];

            sectionElement.appendChild(
                CBUIStringEditor2.createObjectPropertyEditorElement(
                    spec,
                    "CSSClassNames",
                    "CSS Class Names",
                    specChangedCallback
                )
            );
        }
        /* CSS class names */


        /* local CSS */
        {
            let elements = CBUI.createElementTree(
                "CBUI_sectionContainer",
                "CBUI_section"
            );

            element.appendChild(
                elements[0]
            );

            let sectionElement = elements[1];

            sectionElement.appendChild(
                CBUIStringEditor2.createObjectPropertyEditorElement(
                    spec,
                    "localCSSTemplate",
                    "Local CSS Template",
                    specChangedCallback
                )
            );
        }
        /* local CSS */


        /* is article */
        {
            let elements = CBUI.createElementTree(
                "CBUI_sectionContainer",
                "CBUI_section",
                "CBUI_sectionItem"
            );

            element.appendChild(
                elements[0]
            );

            let sectionItemElement = elements[2];

            sectionItemElement.appendChild(
                CBUIBooleanEditor.create(
                    {
                        labelText: "Page Content is an Article",
                        propertyName: "isArticle",
                        spec: args.spec,
                        specChangedCallback: args.specChangedCallback,
                    }
                ).element
            );
        }
        /* is article */


        /* custom layout */

        element.appendChild(
            CBUI.createSectionHeader(
                {
                    text: "Custom Layout",
                }
            )
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

            sectionElement.appendChild(
                CBUIStringEditor2.createObjectPropertyEditorElement(
                    spec,
                    "customLayoutClassName",
                    "Custom Layout Class Name",
                    specChangedCallback
                )
            );

            var propertiesAsJSON = "{\n\n}";

            if (typeof args.spec.customLayoutProperties === "object") {
                propertiesAsJSON = JSON.stringify(
                    args.spec.customLayoutProperties,
                    undefined,
                    2
                );
            }

            var propertiesSpec = {
                propertiesAsJSON: propertiesAsJSON
            };

            let sectionItemElement = CBUI.createElement(
                "CBUI_sectionItem"
            );

            sectionElement.appendChild(
                sectionItemElement
            );

            let stringEditorElement = (
                CBUIStringEditor2.createObjectPropertyEditorElement(
                    propertiesSpec,
                    "propertiesAsJSON",
                    "Custom Layout Properties",
                    function () {
                        CBPageLayoutEditor.propertiesChanged(
                            {
                                propertiesSpec: propertiesSpec,
                                sectionItem: sectionItemElement,
                                spec: spec,
                                specChangedCallback: specChangedCallback,
                            }
                        );
                    }
                )
            );

            stringEditorElement.classList.add(
                "CBUI_flexGrow"
            );

            sectionItemElement.appendChild(
                stringEditorElement
            );
        }
        /* custom layout */


        return element;
    },
    /* CBUISpecEditor_createEditorElement() */


    /**
     * @param string? propertiesSpec.propertiesAsJSON
     * @param Element sectionItem
     * @param object spec
     * @param function specChangedCallback
     *
     * @return undefined
     */
    propertiesChanged: function (args) {
        do {
            try {
                if (typeof args.propertiesSpec.propertiesAsJSON !== "string") {
                    break;
                }
            } catch (error) {
                break;
            }

            var valueAsJSON = args.propertiesSpec.propertiesAsJSON.trim();

            if (valueAsJSON === "") {
                break;
            }

            var value;

            try {
                value = JSON.parse(valueAsJSON);
            } catch (error) {
                args.sectionItem.style.backgroundColor = "hsl(0, 100%, 90%)";
                return;
            }

            if (typeof value !== "object") {
                args.sectionItem.style.backgroundColor = "hsl(0, 100%, 90%)";
                return;
            }

            args.sectionItem.style.backgroundColor = "white";
            args.spec.customLayoutProperties = value;
            args.specChangedCallback();

            return;
        } while (false);

        args.sectionItem.style.backgroundColor = "white";
        args.spec.customLayoutProperties = {};
    },
    /* propertiesChanged() */


    /**
     * @param object spec
     *
     * @return string|undefined
     */
    CBUISpec_toDescription: function (spec) {
        let description = CBModel.valueToString(
            spec,
            "customLayoutClassName"
        ).trim();

        if (description === "") {
            return undefined;
        }

        return description;
    },
    /* CBUISpec_toDescription() */
};
