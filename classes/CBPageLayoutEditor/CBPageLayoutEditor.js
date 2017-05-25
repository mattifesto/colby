"use strict"; /* jshint strict: global */ /* jshint esversion: 6 */
/* global
    CBUI,
    CBUIBooleanEditor,
    CBUIStringEditor */

var CBPageLayoutEditor = {

    /**
     * @param object args.spec
     * @param function args.specChangedCallback
     *
     * @return Element
     */
    createEditor: function (args) {
        var section, item;
        var element = document.createElement("div");
        element.className = "CBPageLayoutEditor";

        element.appendChild(CBUI.createSectionHeader({
            paragraphs: [`
                CSSClassNames can provide additional options. "CBLightTheme" and
                "CBDarkTheme" are two examples of class names that work here.
            `],
        }));

        /* CSS class names */

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

        /* local CSS */

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

        /* is article */

        element.appendChild(CBUI.createHalfSpace());

        section = CBUI.createSection();
        item = CBUI.createSectionItem();
        item.appendChild(CBUIBooleanEditor.create({
            labelText : "Page Content is an Article",
            propertyName : "isArticle",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);
        element.appendChild(section);

        /* custom layout */

        element.appendChild(CBUI.createHalfSpace());
        element.appendChild(CBUI.createSectionHeader({
            text : "Custom Layout",
        }));

        section = CBUI.createSection();
        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "Custom Layout Class Name",
            propertyName : "customLayoutClassName",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        var propertiesAsJSON = "{\n\n}";

        if (typeof args.spec.customLayoutProperties === "object") {
            propertiesAsJSON = JSON.stringify(args.spec.customLayoutProperties, undefined, 2);
        }

        var propertiesSpec = { propertiesAsJSON: propertiesAsJSON };

        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "Custom Layout Properties",
            propertyName : "propertiesAsJSON",
            spec : propertiesSpec,
            specChangedCallback : CBPageLayoutEditor.propertiesChanged.bind(undefined, {
                propertiesSpec: propertiesSpec,
                sectionItem: item,
                spec: args.spec,
                specChangedCallback: args.specChangedCallback,
            }),
        }).element);
        section.appendChild(item);
        element.appendChild(section);

        return element;
    },

    /**
     * @param string? propertiesSpec.propertiesAsJSON
     * @param Element sectionItem
     * @param object spec
     * @param function specChangedCallback
     *
     * @return undefined
     */
    propertiesChanged: function (args) {
        do {
            try {
                if (typeof args.propertiesSpec.propertiesAsJSON !== "string") {
                    break;
                }
            } catch (error) {
                break;
            }

            var valueAsJSON = args.propertiesSpec.propertiesAsJSON.trim();

            if (valueAsJSON === "") {
                break;
            }

            var value;

            try {
                value = JSON.parse(valueAsJSON);
            } catch (error) {
                args.sectionItem.style.backgroundColor = "hsl(0, 100%, 90%)";
                return;
            }

            if (typeof value !== "object") {
                args.sectionItem.style.backgroundColor = "hsl(0, 100%, 90%)";
                return;
            }

            args.sectionItem.style.backgroundColor = "white";
            args.spec.customLayoutProperties = value;
            args.specChangedCallback();

            return;
        } while (false);

        args.sectionItem.style.backgroundColor = "white";
        args.spec.customLayoutProperties = {};
    },

    /**
     * @param string? spec.customLayoutClassName
     *
     * @return string|undefined
     */
    specToDescription : function (spec) {
        return spec.customLayoutClassName;
    },
};
