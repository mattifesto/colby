"use strict";

var CBTextViewEditorFactory = {

    /**
     * @param {function}    handleSpecChanged
     * @param {string}      labelText
     * @param {Object}      spec
     *
     * @return Element
     */
    createEditor : function(args) {
        var element         = document.createElement("div");
        element.className   = "CBTextViewEditor";

        element.appendChild(CBStringEditorFactory.createSingleLineEditor({
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : args.labelText || "Text",
            propertyName        : "text",
            spec                : args.spec }));

        return element;
    },

    /**
     * @return {string}
     */
    widgetClassName : function() {
        return "CBTextViewEditorWidget";
    }
};
