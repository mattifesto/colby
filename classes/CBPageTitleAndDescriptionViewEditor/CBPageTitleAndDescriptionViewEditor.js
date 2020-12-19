"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBPageTitleAndDescriptionViewEditor */
/* globals
    CBUI,
    CBUIBooleanEditor,
    CBUIStringEditor2,
*/


var CBPageTitleAndDescriptionViewEditor = {

    /**
     * @param object args.spec
     * @param object args.specChangedCallback
     *
     * @return Element
     */
    CBUISpecEditor_createEditorElement: function (
        args
    ) {
        let spec = args.spec;
        let specChangedCallback = args.specChangedCallback;
        let element;

        {
            let elements = CBUI.createElementTree(
                "CBPageTitleAndDescriptionViewEditor",
                "CBUI_sectionContainer",
                "CBUI_section",
                "CBUI_sectionItem"
            );

            element = elements[0];

            let sectionItem = elements[3];

            sectionItem.appendChild(
                CBUIBooleanEditor.create(
                    {
                        labelText: "Show Publication Date",
                        propertyName: "showPublicationDate",
                        spec: spec,
                        specChangedCallback: specChangedCallback,
                    }
                ).element
            );
        }

        /* CSSClassNames */

        element.appendChild(CBUI.createHalfSpace());

        element.appendChild(CBUI.createSectionHeader({
            paragraphs: [
                `
                View Specific CSS Class Names:
                `,`
                "custom": disable the default view styles.
                `
            ],
        }));

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
                    "stylesTemplate",
                    "Local CSS Template",
                    specChangedCallback
                )
            );
        }

        return element;
    },
    /* CBUISpecEditor_createEditorElement() */

};
