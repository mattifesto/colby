"use strict";

/**
 * 2015.04.16
 * These editors are expected to be used extensively which means that their
 * structure will not be able to be changed in the future without causing
 * issues. To mitigate this, there is a containing `div` element and the
 * `label` element does not contain the form element (`input` or `textarea`).
 * This makes for a bit more code than would be required in many situations
 * but it allows for flexibility in layout that will hopefully mitigate the
 * need to write custom versions of this code in most cases.
 */
var CBStringEditorFactory = {

    /**
     * @param {function}    handleSpecChanged
     * @param {string{      labelText
     * @param {string}      propertyName
     * @param {Object}      spec
     *
     * @return {Element}
     */
    createMultiLineEditor : function(args) {
        var ID              = Colby.random160();
        var element         = document.createElement("div");
        element.className   = "CBStringEditor";
        var label           = document.createElement("label");
        label.htmlFor       = ID;
        label.textContent   = args.labelText || "";
        var textarea        = document.createElement("textarea");
        textarea.id         = ID;
        textarea.value      = args.spec[args.propertyName] || "";

        textarea.addEventListener("input", CBStringEditorFactory.handleInput.bind(undefined, {
            element             : textarea,
            handleSpecChanged   : args.handleSpecChanged,
            propertyName        : args.propertyName,
            spec                : args.spec }));

        element.appendChild(label);
        element.appendChild(textarea);

        return element;
    },

    /**
     * @param {Array}       data
     * @param {string}      dataUpdatedEvent
     * @param {function}    handleSpecChanged
     * @param {string{      labelText
     * @param {string}      propertyName
     * @param {Object}      spec
     *
     * @return {Element}
     */
    createSelectEditor : function(args) {
        var ID              = Colby.random160();
        var element         = document.createElement("div");
        element.className   = "CBStringEditor";
        var label           = document.createElement("label");
        label.htmlFor       = ID;
        label.textContent   = args.labelText || "";
        var select          = document.createElement("select");
        select.id           = ID;

        select.addEventListener("change", CBStringEditorFactory.handleInput.bind(undefined, {
            element             : select,
            handleSpecChanged   : args.handleSpecChanged,
            propertyName        : args.propertyName,
            spec                : args.spec
        }));

        var handler = CBStringEditorFactory.handleSelectDataUpdated.bind(undefined, {
            data            : args.data,
            propertyName    : args.propertyName,
            selectElement   : select,
            spec            : args.spec,
        });

        handler.call();

        if (args.dataUpdatedEvent) {
            document.addEventListener(args.dataUpdatedEvent, handler);
        }

        element.appendChild(label);
        element.appendChild(select);

        return element;
    },

    /**
     * @param {function}    handleSpecChanged
     * @param {string{      labelText
     * @param {string}      propertyName
     * @param {Object}      spec
     *
     * @return {Element}
     */
    createSingleLineEditor : function(args) {
        var ID              = Colby.random160();
        var element         = document.createElement("div");
        element.className   = "CBStringEditor";
        var label           = document.createElement("label");
        label.htmlFor       = ID;
        label.textContent   = args.labelText || "";
        var input           = document.createElement("input");
        input.id            = ID;
        input.type          = "text";
        input.value         = args.spec[args.propertyName] || "";

        input.addEventListener("input", CBStringEditorFactory.handleInput.bind(undefined, {
            element             : input,
            handleSpecChanged   : args.handleSpecChanged,
            propertyName        : args.propertyName,
            spec                : args.spec }));

        element.appendChild(label);
        element.appendChild(input);

        return element;
    },

    /**
     * @param   {Element}   element
     * @param   {function}  handleSpecChanged
     * @param   {string}    propertyName
     * @param   {Object}    spec
     *
     * @return  undefined
     */
    handleInput : function(args) {
        args.spec[args.propertyName] = args.element.value;

        args.handleSpecChanged.call();
    },

    /**
     * @param   {Array}     data
     * @param   {string}    propertyName
     * @param   {Element}   selectElement
     * @param   {Object}    spec
     *
     * @return  undefined
     */
    handleSelectDataUpdated : function(args) {
        var childElement;
        var selectedValue   = args.spec[args.propertyName];
        var valueWasFound   = false;

        while (childElement = args.selectElement.firstChild) {
            args.selectElement.removeChild(childElement);
        }

        args.data.forEach(function(item) {
            var option          = document.createElement("option");
            option.textContent  = item.textContent;
            option.value        = item.value;

            args.selectElement.appendChild(option);

            if (item.value == selectedValue) {
                valueWasFound = true;
            }
        });

        /**
         * If the selected value wasn't found in the list of options and the
         * value isn't empty, created a deprecated option to potentially
         * preserve the value if the list of options is incorrect for some
         * reason.
         */

        if (!valueWasFound && selectedValue) {
            var option          = document.createElement("option");
            option.textContent  = "(Original Value)";
            option.value        = selectedValue;

            args.selectElement.appendChild(option);
        }

        args.selectElement.value = selectedValue;
    }
};
