"use strict"; /* jshint strict: global */ /* jshint esversion: 6 */
/* global
    CBUI,
    CBUIBooleanEditor,
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
                Supported Class Names:
                `,`
                CBLightTheme: Theme with a light background and dark text.
                `,`
                CBDarkTheme: Theme with a dark background and light text.
                `,`
                center: Center the text.
                `,`
                justify: Justify the text.
                `,`
                right: Align the text to the right.
                `,`
                hero1: Make the text large for marketing presentations. Heading
                levels 1-6 provide diverse font sizes.
                `,`
                To remove the default styles and produce a completely custom
                presentation enable the Custom option.
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

        item = CBUI.createSectionItem();
        item.appendChild(CBUIBooleanEditor.create({
            labelText: "Custom",
            propertyName: "isCustom",
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
