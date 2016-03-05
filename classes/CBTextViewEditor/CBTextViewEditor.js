"use strict";

var CBTextViewEditor = {

    /**
     * @param string args.labelText
     * @param object args.spec
     * @param function args.specChangedCallback
     *
     * @return Element
     */
    createEditor : function(args) {
        var element = document.createElement("div");
        element.className = "CBTextViewEditor";
        var section = CBUI.createSection();

        /* text */
        var item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText : args.labelText || "Text",
            propertyName : "text",
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
        return spec.text;
    },
};
