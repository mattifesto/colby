"use strict";

var CBKeyValuePairEditor = {

    /**
     * @param object args.spec
     * @param object args.specChangedCallback
     *
     * @return Element
     */
    createEditor : function (args) {
        var item;
        var element = document.createElement("div");
        element.className = "CBKeyValuePairEditor";
        var section = CBUI.createSection();

        /* key */
        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "Key",
            propertyName : "key",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        /* value */
        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : "Value As JSON",
            propertyName : "valueAsJSON",
            spec : args.spec,
            specChangedCallback : args.specChangedCallback,
        }).element);
        section.appendChild(item);

        element.appendChild(section);

        return element;
    },

    /**
     * @param string? spec.key
     * @param string? spec.valueAsJSON
     *
     * @return string|undefined
     */
    specToDescription : function (spec) {
        if (spec.key === undefined) {
            return undefined;
        } else {
            return spec.key + " : " + spec.valueAsJSON;
        }
    },
};
