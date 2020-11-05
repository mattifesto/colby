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
    CBUIStringEditor,
*/

var CBTextView2Editor = {

    /**
     * @param object args.spec
     * @param function args.specChangedCallback
     *
     * @return Element
     */
    createEditor: function (args) {
        var section, item;
        var element = CBUI.createElement("CBTextView2Editor");

        /* convert to CBMessageView */
        {
            let sectionContainerElement = CBUI.createElement(
                "CBUI_sectionContainer"
            );

            element.appendChild(sectionContainerElement);

            let sectionElement = CBUI.createElement(
                "CBUI_section"
            );

            sectionContainerElement.appendChild(sectionElement);

            let actionElement = CBUI.createElement(
                "CBUI_action"
            );

            actionElement.textContent = "Convert to CBMessageView";

            actionElement.addEventListener(
                "click",
                createEditor_convert
            );

            sectionElement.appendChild(actionElement);
        }
        /* convert to CBMessageView */


        element.appendChild(
            CBUI.createHalfSpace()
        );

        section = CBUI.createSection();

        item = CBUI.createSectionItem();

        item.appendChild(
            CBUIStringEditor.createEditor(
                {
                    labelText: "Content (CommonMark)",
                    propertyName: "contentAsCommonMark",
                    spec: args.spec,
                    specChangedCallback: args.specChangedCallback,
                }
            ).element
        );

        section.appendChild(item);
        element.appendChild(section);

        /* CSSClassNames */

        element.appendChild(
            CBUI.createHalfSpace()
        );

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

        section = CBUI.createSection();
        item = CBUI.createSectionItem();

        item.appendChild(
            CBUIStringEditor.createEditor(
                {
                    labelText: "CSS Class Names",
                    propertyName: "CSSClassNames",
                    spec: args.spec,
                    specChangedCallback: args.specChangedCallback,
                }
            ).element
        );

        section.appendChild(item);
        element.appendChild(section);

        /* localCSSTemplate */

        element.appendChild(
            CBUI.createHalfSpace()
        );

        section = CBUI.createSection();

        item = CBUI.createSectionItem();

        item.appendChild(
            CBUIStringEditor.createEditor(
                {
                    labelText: "Local CSS Template",
                    propertyName: "localCSSTemplate",
                    spec: args.spec,
                    specChangedCallback: args.specChangedCallback,
                }
            ).element
        );

        section.appendChild(item);
        element.appendChild(section);

        element.appendChild(
            CBUI.createHalfSpace()
        );

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
    /* createEditor() */


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
