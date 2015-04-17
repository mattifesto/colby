"use strict";

var CBBooleanEditorFactory = {

    /**
     * @param {function}    handleSpecChanged
     * @param {string{      labelText
     * @param {string}      propertyName
     * @param {Object}      spec
     *
     * @return {Element}
     */
    createCheckboxEditor : function(args) {
        var ID              = Colby.random160();
        var element         = document.createElement("div");
        element.className   = "CBBooleanEditor";
        var label           = document.createElement("label");
        label.htmlFor       = ID;
        label.textContent   = args.labelText || "";
        var input           = document.createElement("input");
        input.id            = ID;
        input.type          = "checkbox";
        input.checked       = args.spec[args.propertyName] || false;

        input.addEventListener("change", CBBooleanEditorFactory.handleCheckboxChanged.bind(undefined, {
            handleSpecChanged   : args.handleSpecChanged,
            inputElement        : input,
            propertyName        : args.propertyName,
            spec                : args.spec }));

        element.appendChild(input);
        element.appendChild(label);

        return element;
    },

    /**
     * @param {function}    handleSpecChanged
     * @param {Element}     inputElement
     * @param {string}      propertyName
     * @param {Object}      spec
     *
     * @return {undefined}
     */
    handleCheckboxChanged : function(args) {
        args.spec[args.propertyName] = args.inputElement.checked;

        args.handleSpecChanged.call();
    }
};
