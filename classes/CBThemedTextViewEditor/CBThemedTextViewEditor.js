"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBThemedTextViewEditor */
/* globals
    CBUI,
    CBUIBooleanEditor,
    CBUIStringEditor,
*/

var CBThemedTextViewEditor = {

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

        element.appendChild(CBUI.createButton({
            text: "Convert to CBTextView2",
            callback: function () {
                var value;
                var CSSClassNames = [];
                var localCSSRulesets = [];

                if (args.spec.center) {
                    CSSClassNames.push("center");
                }

                value = args.spec.titleColor;
                if (typeof value === "string" && (value = value.trim())) {
                    localCSSRulesets.push("view > .content > h1:first-child { color: " + value + " }");
                }

                value = args.spec.contentColor;
                if (typeof value === "string" && (value = value.trim())) {
                    localCSSRulesets.push("view > .content { color: " + value + " }");
                }

                value = args.spec.stylesTemplate;
                if (typeof value === "string" && (value = value.trim())) {
                    localCSSRulesets.push(value);
                }

                var content = [];

                value = args.spec.titleAsMarkaround;
                if (typeof value === "string" && (value = value.trim())) {
                    content.push("# " + value);
                }

                value = args.spec.contentAsMarkaround;
                if (typeof value === "string" && (value = value.trim())) {
                    content.push(value);
                }

                var spec = {
                    className: "CBTextView2",
                    contentAsCommonMark: content.join("\n\n"),
                    CSSClassNames: CSSClassNames.join(" "),
                    localCSSTemplate: localCSSRulesets.join("\n\n"),
                };

                var specAsJSON = JSON.stringify(spec);
                localStorage.setItem("specClipboard", specAsJSON);

                alert("The CBTextView2 is now on the clipboard. You can paste it after this view, compare the two and make revisions, then delete this view.");
            },
        }).element);

        element.appendChild(CBUI.createHalfSpace());

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
