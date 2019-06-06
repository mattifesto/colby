"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBPageListView2Editor */
/* global
    CBModel,
    CBUI,
    CBUIStringEditor,
*/

var CBPageListView2Editor = {

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
    createEditor: function (args) {
        var section, item;
        var element = document.createElement("div");
        element.className = "CBPageListView2Editor";

        element.appendChild(
            CBUI.createHalfSpace()
        );

        section = CBUI.createSection();

        item = CBUI.createSectionItem();

        item.appendChild(
            CBUIStringEditor.createEditor(
                {
                    labelText: "Class Name for Kind",
                    propertyName: "classNameForKind",
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
                        View Specific CSS Class Names
                        `,`
                        "custom": disable the default view styles.
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

        element.appendChild(
            CBUI.createHalfSpace()
        );

        return element;
    },
    /* createEditor() */


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
