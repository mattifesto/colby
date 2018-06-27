"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBThemedTextViewEditor */
/* globals
    CBUI,
    CBUIBooleanEditor,
    CBUISectionItem4,
    CBUISpecClipboard,
    CBUIStringEditor,
    CBUIStringsPart,
*/

var CBThemedTextViewEditor = {

    /**
     * @param model spec
     *
     * @return undefined
     */
    convertToCBTextView2: function (spec) {
        var value;
        var CSSClassNames = [];
        var localCSSRulesets = [];

        if (spec.center) {
            CSSClassNames.push("center");
        }

        value = spec.titleColor;
        if (typeof value === "string" && (value = value.trim())) {
            localCSSRulesets.push("view > .content > h1:first-child { color: " + value + " }");
        }

        value = spec.contentColor;
        if (typeof value === "string" && (value = value.trim())) {
            localCSSRulesets.push("view > .content { color: " + value + " }");
        }

        value = spec.stylesTemplate;
        if (typeof value === "string" && (value = value.trim())) {
            localCSSRulesets.push(value);
        }

        var content = [];

        value = spec.titleAsMarkaround;
        if (typeof value === "string" && (value = value.trim())) {
            content.push("# " + value);
        }

        value = spec.contentAsMarkaround;
        if (typeof value === "string" && (value = value.trim())) {
            content.push(value);
        }

        let convertedSpec = {
            className: "CBTextView2",
            contentAsCommonMark: content.join("\n\n"),
            CSSClassNames: CSSClassNames.join(" "),
            localCSSTemplate: localCSSRulesets.join("\n\n"),
        };

        CBUISpecClipboard.specs = [convertedSpec];

        alert("The CBTextView2 is now on the clipboard. You can paste it after this view, compare the two and make revisions, then delete this view.");
    },

    /**
     * @param object spec
     * @param function specChangedCallback
     *
     * @return Element
     */
    createEditor : function(args) {
        var element = document.createElement("div");
        element.className = "CBThemedTextViewEditor";

        element.appendChild(CBUI.createHalfSpace());

        {
            let sectionElement = CBUI.createSection();

            {
                let sectionItem = CBUISectionItem4.create();
                sectionItem.callback = function () {
                    CBThemedTextViewEditor.convertToCBTextView2(args.spec);
                };

                let stringsPart = CBUIStringsPart.create();
                stringsPart.string1 = "Convert to CBTextView2";

                stringsPart.element.classList.add("action");

                sectionItem.appendPart(stringsPart);
                sectionElement.appendChild(sectionItem.element);
            }

            element.appendChild(sectionElement);
            element.appendChild(CBUI.createHalfSpace());
        }

        {
            let section = CBUI.createSection();

            {
                let item = CBUI.createSectionItem();
                item.appendChild(CBUIStringEditor.createEditor({
                    labelText : "Title",
                    propertyName : "titleAsMarkaround",
                    spec : args.spec,
                    specChangedCallback : args.specChangedCallback,
                }).element);
                section.appendChild(item);
            }

            {
                let item = CBUI.createSectionItem();
                item.appendChild(CBUIStringEditor.createEditor({
                    labelText : "Content",
                    propertyName : "contentAsMarkaround",
                    spec : args.spec,
                    specChangedCallback : args.specChangedCallback,
                }).element);
                section.appendChild(item);
            }

            element.appendChild(section);
            element.appendChild(CBUI.createHalfSpace());
        }

        {
            let section = CBUI.createSection();

            {
                let item = CBUI.createSectionItem();
                item.appendChild(CBUIStringEditor.createEditor({
                    labelText : "URL",
                    propertyName : "URL",
                    spec : args.spec,
                    specChangedCallback : args.specChangedCallback,
                }).element);
                section.appendChild(item);
            }

            element.appendChild(section);
            element.appendChild(CBUI.createHalfSpace());
        }

        {
            let section = CBUI.createSection();

            /* titleColor */
            {
                let item = CBUI.createSectionItem();
                item.appendChild(CBUIStringEditor.createEditor({
                    labelText : "Title Color",
                    propertyName : "titleColor",
                    spec : args.spec,
                    specChangedCallback : args.specChangedCallback,
                }).element);
                section.appendChild(item);
            }

            /* contentColor */
            {
                let item = CBUI.createSectionItem();
                item.appendChild(CBUIStringEditor.createEditor({
                    labelText : "Content Color",
                    propertyName : "contentColor",
                    spec : args.spec,
                    specChangedCallback : args.specChangedCallback,
                }).element);
                section.appendChild(item);
            }

            /* center */
            {
                let item = CBUI.createSectionItem();
                item.appendChild(CBUIBooleanEditor.create({
                    labelText : "Center",
                    propertyName : "center",
                    spec : args.spec,
                    specChangedCallback : args.specChangedCallback,
                }).element);
                section.appendChild(item);
            }

            element.appendChild(section);
            element.appendChild(CBUI.createHalfSpace());
        }

        {
            let section = CBUI.createSection();

            {
                let item = CBUI.createSectionItem();

                item.appendChild(CBUIStringEditor.createEditor({
                    labelText : "Styles Template",
                    propertyName : "stylesTemplate",
                    spec : args.spec,
                    specChangedCallback : args.specChangedCallback,
                }).element);
                section.appendChild(item);
            }

            element.appendChild(section);
            element.appendChild(CBUI.createHalfSpace());
        }

        return element;
    },

    /**
     * @param string? spec.contentAsMarkaround
     * @param string? spec.titleAsMarkaround
     *
     * @return string|undefined
     */
    specToDescription : function (spec) {
        return spec.titleAsMarkaround || spec.contentAsMarkaround;
    },
};
