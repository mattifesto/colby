"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBPageTitleAndDescriptionViewEditor */
/* globals
    CBUI,
    CBUIBooleanEditor,
    CBUIStringEditor,
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
        var section, item;

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

        section = CBUI.createSection();

        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText: "CSS Class Names",
            propertyName: "CSSClassNames",
            spec: args.spec,
            specChangedCallback: args.specChangedCallback,
        }).element);
        section.appendChild(item);
        element.appendChild(section);

        /* localCSSTemplate (uses stylesTemplate property on this view) */

        element.appendChild(CBUI.createHalfSpace());

        section = CBUI.createSection();
        item = CBUI.createSectionItem();

        item.appendChild(CBUIStringEditor.createEditor({
            labelText: "Local CSS Template",
            propertyName: "stylesTemplate",
            spec: args.spec,
            specChangedCallback: args.specChangedCallback,
        }).element);
        section.appendChild(item);
        element.appendChild(section);

        element.appendChild(CBUI.createHalfSpace());

        return element;
    },
    /* CBUISpecEditor_createEditorElement() */

};
