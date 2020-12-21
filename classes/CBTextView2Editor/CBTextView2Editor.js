"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBTextView2Editor */
/* global
    CBAjax,
    CBModel,
    CBUI,
    CBUINavigationView,
    CBUIPanel,
    CBUISpecClipboard,
    CBUISpecEditor,
    CBUIStringEditor2,
*/

var CBTextView2Editor = {

    /**
     * @param object args.spec
     * @param function args.specChangedCallback
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
                "CBTextView2Editor",
                "CBUI_sectionContainer",
                "CBUI_section",
                "CBUI_action"
            );

            element = elements[0];

            /* convert to CBMessageView */

            let actionElement = elements[3];

            actionElement.textContent = "Convert to CBMessageView";

            actionElement.addEventListener(
                "click",
                createEditor_convert
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
                    "contentAsCommonMark",
                    "Content (CommonMark)",
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
                        "center": center the text.
                        `,`
                        "justify": justify the text.
                        `,`
                        "right": align the text to the right.
                        `,`
                        "hero1": large text for marketing presentations.
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
                    "localCSSTemplate",
                    "Local CSS Template",
                    specChangedCallback
                )
            );
        }

        return element;



        /* -- closures -- -- -- -- -- */



        /**
         * @return undefined
         */
        function createEditor_convert() {
            CBUIPanel.confirmText(
                `
                    Are you sure you want to convert this CBTextView2 into a
                    CBMessageView?
                `
            ).then(
                function (wasConfirmed) {
                    if (!wasConfirmed) {
                        return;
                    }

                    CBUISpecClipboard.specs = [args.spec];

                    return CBAjax.call(
                        "CBTextView2",
                        "convertToCBMessageView",
                        args.spec
                    ).then(
                        function (messageViewSpec) {
                            Object.assign(args.spec, messageViewSpec);

                            args.specChangedCallback();

                            let editor = CBUISpecEditor.create(
                                {
                                    spec: args.spec,
                                    specChangedCallback: args.specChangedCallback,
                                }
                            );

                            CBUINavigationView.replace(
                                {
                                    element: editor.element,
                                    title: args.spec.className,
                                }
                            );
                        }
                    );
                }
            ).catch(
                function (error) {
                    CBUIPanel.displayAndReportError(error);
                }
            );
        }
        /* createEditor_convert() */

    },
    /* CBUISpecEditor_createEditorElement() */



    /**
     * @param object spec
     *
     * @return string|undefined
     */
    CBUISpec_toDescription: function (spec) {
        let content = CBModel.valueToString(
            spec,
            "contentAsCommonMark"
        ).trim();

        return (content === "") ? undefined : content;
    },
    /* CBUISpec_toDescription() */

};
/* CBTextView2Editor */
