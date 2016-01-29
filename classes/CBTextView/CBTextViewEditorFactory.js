"use strict";

var CBTextViewEditorFactory = {

    /**
     * @param {string}      labelText
     * @param {Object}      spec
     * @param {function}    specChangedCallback
     *
     * @return Element
     */
    createEditor : function(args) {
        var element         = document.createElement("div");
        element.className   = "CBTextViewEditor";

        element.appendChild(CBStringEditorFactory.createSingleLineEditor({
            handleSpecChanged   : args.specChangedCallback,
            labelText           : args.labelText || "Text",
            propertyName        : "text",
            spec                : args.spec }));

        return element;
    },
};
