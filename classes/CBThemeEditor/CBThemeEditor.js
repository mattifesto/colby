"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBThemeEditor */
/* global
    CBModel,
    CBUI,
    CBUIStringEditor2,
*/


var CBThemeEditor = {

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
        let element, sectionElement;

        {
            let elements = CBUI.createElementTree(
                "CBThemeEditor",
                "CBUI_sectionContainer",
                "CBUI_section"
            );

            element = elements[0];

            sectionElement = elements[2];

            let properties = [
                {
                    name: "title",
                    labelText: "Title"
                },
                {
                    name: "classNameForKind",
                    labelText: "Class Name for Kind"
                },
                {
                    name: "classNameForTheme",
                    labelText: "Class Name for Theme"
                },
                {
                    name: "description",
                    labelText: "Description"
                },
            ];

            properties.forEach(
                function (
                    property
                ) {
                    sectionElement.appendChild(
                        CBUIStringEditor2.createObjectPropertyEditorElement(
                            spec,
                            property.name,
                            property.labelText,
                            specChangedCallback
                        )
                    );
                }
            );
        }

        // styles
        {
            let stylesEditor = CBUIStringEditor2.create();

            stylesEditor.CBUIStringEditor2_initializeObjectPropertyEditor(
                spec,
                "styles",
                "Styles",
                specChangedCallback
            );

            sectionElement.appendChild(
                stylesEditor.CBUIStringEditor2_getElement()
            );

            // add style

            let elements = CBUI.createElementTree(
                "CBUI_container1",
                "CBUI_button1"
            );

            element.appendChild(
                elements[0]
            );

            let buttonElement = elements[1];
            buttonElement.textContent = "Add Style";

            buttonElement.addEventListener(
                "click",
                function () {
                    let styles = CBModel.valueToString(
                        spec,
                        "styles"
                    ).trim();

                    let pre = (styles !== "") ? "\n\n" : "";

                    spec.styles = styles.replace(
                        /[\s]*$/,
                        pre + "view {\n\n}"
                    );

                    stylesEditor.CBUIStringEditor2_setValue(
                        spec.styles
                    );

                    specChangedCallback();
                }
            );
        }

        return element;
    },
    /* CBUISpecEditor_createEditorElement() */

};
