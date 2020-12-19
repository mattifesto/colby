"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBPageListView2Editor */
/* global
    CBModel,
    CBUI,
    CBUIStringEditor2,
*/



var CBPageListView2Editor = {

    /* -- CBUISpecEditor interfaces -- -- -- -- -- */



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
    CBUISpecEditor_createEditorElement(
        args
    ) {
        let spec = args.spec;
        let specChangedCallback = args.specChangedCallback;

        let elements = CBUI.createElementTree(
            "CBPageListView2Editor",
            "CBUI_sectionContainer",
            "CBUI_section"
        );

        let element = elements[0];
        let sectionElement = elements[2];

        sectionElement.appendChild(
            CBUIStringEditor2.createObjectPropertyEditorElement(
                spec,
                "classNameForKind",
                "Class Name for Kind",
                specChangedCallback
            )
        );

        sectionElement.appendChild(
            CBUIStringEditor2.createObjectPropertyEditorElement(
                spec,
                "maximumPageCount",
                "Maximum Page Count",
                specChangedCallback
            )
        );

        /* CSSClassNames */

        element.appendChild(
            CBUI.createHalfSpace()
        );

        element.appendChild(
            CBUI.createSectionHeader(
                {
                    paragraphs: [
                        `
                            View Specific CSS Class Names
                        `,
                        `
                            custom: disable the default view styles
                        `,
                        `
                            CBPageListView2_small: show thumbnails in a tight
                            horizontally flowing layout
                        `,
                    ],
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
                    "CSSClassNames",
                    "CSS Class Names",
                    specChangedCallback
                )
            );

        }

        return element;
    },
    /* CBUISpecEditor_createEditorElement() */



    /**
     * @param object spec
     *
     * @return string|undefined
     */
    CBUISpec_toDescription: function (spec) {
        let description = CBModel.valueToString(
            spec,
            "classNameForKind"
        ).trim() || undefined;

        return description;
    },
    /* CBUISpec_toDescription() */

};
/* CBPageListView2Editor */
