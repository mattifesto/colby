"use strict"; /* jshint strict: global */ /* jshint esversion: 6 */
/* global
    CBUI,
    CBUIStringEditor */

var CBCustomViewEditor = {

    /**
     * @param object args.spec
     * @param function args.specChangedCallback
     *
     * @return Element
     */
    createEditor: function (args) {
        var section, item;
        var element = document.createElement("div");
        element.className = "CBCustomViewEditor";

        /* custom view class name */

        section = CBUI.createSection();
        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "Custom View Class Name",
            propertyName : "customViewClassName",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);
        element.appendChild(section);

        /* custom properties */

        element.appendChild(CBUI.createHalfSpace());
        element.appendChild(CBUI.createSectionHeader({
            text : "Custom Properties",
        }));

        var propertiesAsJSON = "{\n\n}";

        if (typeof args.spec.properties === "object") {
            propertiesAsJSON = JSON.stringify(args.spec.properties, undefined, 2);
        }

        var propertiesSpec = { propertiesAsJSON: propertiesAsJSON };

        section = CBUI.createSection();
        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "Properties",
            propertyName : "propertiesAsJSON",
            spec : propertiesSpec,
            specChangedCallback : CBCustomViewEditor.propertiesChanged.bind(undefined, {
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
            args.spec.properties = value;
            args.specChangedCallback();

            return;
        } while (false);

        args.sectionItem.style.backgroundColor = "white";
        args.spec.properties = {};
    },

    /**
     * @param string? spec.customViewClassName
     *
     * @return string|undefined
     */
    specToDescription : function (spec) {
        return spec.customViewClassName;
    },
};
