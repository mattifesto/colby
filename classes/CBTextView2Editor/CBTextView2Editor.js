"use strict"; /* jshint strict: global */ /* jshint esversion: 6 */
/* global
    CBUI,
    CBUIStringEditor */

var CBTextView2Editor = {

    /**
     * @param object args.spec
     * @param function args.specChangedCallback
     *
     * @return Element
     */
    createEditor: function (args) {
        var section, item;
        var element = document.createElement("div");
        element.className = "CBTextView2Editor";

        section = CBUI.createSection();

        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "Content (CommonMark)",
            propertyName : "contentAsCommonMark",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
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
        }));

        section = CBUI.createSection();
        item = CBUI.createSectionItem();

        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "CSS Class Names",
            propertyName : "CSSClassNames",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);
        element.appendChild(section);

        /* localCSSTemplate */

        element.appendChild(CBUI.createHalfSpace());

        section = CBUI.createSection();

        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "Local CSS Template",
            propertyName : "localCSSTemplate",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        element.appendChild(section);

        return element;
    },

    /**
     * @param string? spec.text
     *
     * @return string|undefined
     */
    specToDescription : function (spec) {
        return spec.contentAsCommonMark;
    },
};
