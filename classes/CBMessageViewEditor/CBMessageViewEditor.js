"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBMessageViewEditor */
/* global
    CBMessageMarkup,
    CBUI,
    CBUIStringEditor2,
*/

var CBMessageViewEditor = {

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
    CBUISpecEditor_createEditorElement: function (args) {
        let spec = args.spec;
        let specChangedCallback = args.specChangedCallback;

        let element;

        {
            let elements = CBUI.createElementTree(
                "CBMessageViewEditor",
                "CBUI_sectionContainer",
                "CBUI_section"
            );

            element = elements[0];

            let sectionElement = elements[2];

            sectionElement.appendChild(
                CBUIStringEditor2.createObjectPropertyEditorElement(
                    spec,
                    "markup",
                    "Content (Message Markup)",
                    specChangedCallback
                )
            );
        }


        /* CSSClassNames */

        element.appendChild(
            CBUI.createSectionHeader(
                {
                    paragraphs: [
                        `
                        View Specific CSS Class Names:
                        `,`
                        "custom": disable the default view styles.
                        `,`
                        Supported CSS Class Names:
                        `,`
                        "CBLightTheme": light background and dark text.
                        `,`
                        "CBDarkTheme": dark background and light text.
                        `
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

        /* localCSSTemplate */

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
                    "CSSTemplate",
                    "CSS Template",
                    specChangedCallback
                )
            );
        }

        return element;
    },
    /* CBUISpecEditor_createEditorElement() */



    /**
     * @param string? spec.text
     *
     * @return string|undefined
     */
    CBUISpec_toDescription: function (spec) {
        if (spec.markup) {
            var text = CBMessageMarkup.markupToText(spec.markup);

            if (text) {
                var matches = text.match(/^.*$/m);

                if (matches) {
                    return matches[0];
                }
            }
        }

        return undefined;
    },
    /* CBUISpec_toDescription() */

};
