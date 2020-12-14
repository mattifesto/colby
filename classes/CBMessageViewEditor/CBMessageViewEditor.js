"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBMessageViewEditor */
/* global
    CBMessageMarkup,
    CBUI,
    CBUIStringEditor,
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
        var section, item;
        var element = document.createElement("div");
        element.className = "CBMessageViewEditor";

        element.appendChild(CBUI.createHalfSpace());

        section = CBUI.createSection();

        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText: "Content (Message Markup)",
            propertyName: "markup",
            spec: args.spec,
            specChangedCallback: args.specChangedCallback,
        }).element);
        section.appendChild(item);
        element.appendChild(section);

        /* CSSClassNames */

        element.appendChild(CBUI.createHalfSpace());
        element.appendChild(CBUI.createSectionHeader({
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

        /* localCSSTemplate */

        element.appendChild(CBUI.createHalfSpace());

        section = CBUI.createSection();

        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText: "CSS Template",
            propertyName: "CSSTemplate",
            spec: args.spec,
            specChangedCallback: args.specChangedCallback,
        }).element);
        section.appendChild(item);
        element.appendChild(section);

        element.appendChild(CBUI.createHalfSpace());

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
