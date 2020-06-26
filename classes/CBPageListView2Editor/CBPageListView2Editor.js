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
        var section, item;

        let elements = CBUI.createElementTree(
            "CBPageListView2Editor",
            "CBUI_sectionContainer",
            "CBUI_section"
        );

        let element = elements[0];
        let sectionElement = elements[2];

        item = CBUI.createSectionItem();

        item.appendChild(
            CBUIStringEditor.createEditor(
                {
                    labelText: "Class Name for Kind",
                    propertyName: "classNameForKind",
                    spec: spec,
                    specChangedCallback: specChangedCallback,
                }
            ).element
        );

        sectionElement.appendChild(
            item
        );

        let maximumPageCountEditor = CBUIStringEditor.create();
        maximumPageCountEditor.title = "Maximum Page Count";

        maximumPageCountEditor.value = CBModel.valueToString(
            spec,
            "maximumPageCount"
        );

        maximumPageCountEditor.changed = function () {
            spec.maximumPageCount = maximumPageCountEditor.value;
            specChangedCallback();
        };

        sectionElement.appendChild(
            maximumPageCountEditor.element
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
