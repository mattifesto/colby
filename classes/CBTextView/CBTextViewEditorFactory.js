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
        var label           = document.createElement("label");
        label.textContent   = args.labelText || "Text";
        var input           = document.createElement("input");
        input.type          = "text";
        input.value         = args.spec.text || "";

        input.addEventListener("input", CBTextViewEditorFactory.handleInput.bind(undefined, {
            handleSpecChanged   : args.handleSpecChanged,
            inputElement        : input,
            spec                : args.spec }));

        label.appendChild(input);
        element.appendChild(label);

        return element;
    },

    /**
     * @return {string}
     */
    CSSWidth : function() {
        return "400px";
    },

    /**
     * @param {function}    handleSpecChanged
     * @param {Element}     inputElement
     * @param {Object}      spec
     *
     * @return {undefined}
     */
    handleInput : function(args) {
        args.spec.text = args.inputElement.value;

        args.handleSpecChanged.call();
    },

    /**
     * @return {string}
     */
    widgetClassName : function() {
        return "CBTextViewEditorWidget";
    }
};
