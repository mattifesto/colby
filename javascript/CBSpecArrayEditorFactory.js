"use strict";

var CBSpecArrayEditorFactory = {

    /**
     * @param   {Array}     array
     * @param   {function}  handleChanged
     *
     * @return  {Element}
     */
    createEditor : function(args) {
        var element         = document.createElement("div");
        element.className   = "CBArrayEditor";

        args.array.forEach(function(spec) {
            var widgetElement = CBSpecArrayEditorFactory.createWidgetForSpec({
                array           : args.array,
                handleChanged   : args.handleChanged,
                parentElement   : element,
                spec            : spec
            })
            element.appendChild(widgetElement);
        });

        return element;
    },

    /**
     * @param   {Array}     array
     * @param   {function}  handleArrayChanged
     * @param   {Element}   parentElement
     * @param   {Object}    spec
     *
     * @return  undefined
     */
    handleInsert : function(args) {
        var index           = args.array.indexOf(args.spec);
        var beforeElement   = args.parentElement.children.item(index);
        var spec            = {
            className : "CBFabric"
        };
        var widgetElement   = CBSpecArrayEditorFactory.createWidgetForSpec({
            array           : args.array,
            handleChanged   : args.handleArrayChanged,
            parentElement   : args.parentElement,
            spec            : spec
        });

        args.array.splice(index, 0, spec); // insert before spec at index
        args.parentElement.insertBefore(widgetElement, beforeElement);

        args.handleArrayChanged.call();
    },

    /**
     * @param   {Array}     array
     * @param   {function}  handleArrayChanged
     * @param   {Element}   parentElement
     * @param   {Object}    spec
     *
     * @return  undefined
     */
    handleMoveDown : function(args) {
        var index = args.array.indexOf(args.spec);

        if (index < (args.array.length - 1)) {
            var widgetElement   = args.parentElement.children.item(index);
            var afterElement    = widgetElement.nextSibling;

            args.array.splice(index, 1); // remove at index
            args.array.splice(index + 1, 0, args.spec); // insert after previous spec

            args.parentElement.removeChild(widgetElement);
            args.parentElement.insertBefore(widgetElement, afterElement.nextSibling);

            args.handleArrayChanged.call();
        }
    },

    /**
     * @param   {Array}     array
     * @param   {function}  handleArrayChanged
     * @param   {Element}   parentElement
     * @param   {Object}    spec
     *
     * @return  undefined
     */
    handleMoveUp : function(args) {
        var index = args.array.indexOf(args.spec);

        if (index > 0) {
            var widgetElement   = args.parentElement.children.item(index);
            var previousSibling = widgetElement.previousSibling;

            args.array.splice(index, 1); // remove at index
            args.array.splice(index - 1, 0, args.spec); // insert before previous spec

            args.parentElement.removeChild(widgetElement);
            args.parentElement.insertBefore(widgetElement, previousSibling);

            args.handleArrayChanged.call();
        }
    },

    /**
     * @param   {Array}     array
     * @param   {function}  handleArrayChanged
     * @param   {Element}   parentElement
     * @param   {Object}    spec
     *
     * @return  undefined
     */
    handleRemove : function(args) {
        if (confirm("Are you sure you want to remove this item?")) {
            var index           = args.array.indexOf(args.spec);
            var widgetElement   = args.parentElement.children.item(index);

            args.array.splice(index, 1); // remove at index

            args.parentElement.removeChild(widgetElement);

            args.handleArrayChanged.call();
        }
    },

    /**
     * @param   {Array}     array
     * @param   {Element}   parentElement
     * @param   {Object}    spec
     *
     * @return  undefined
     */
    handleSelect : function(args) {
        var element;
        var elements    = args.parentElement.getElementsByClassName("CBEditorWidgetSelected");
        var count       = elements.length;

        for (var i = 0; i < count; i++) {
            element = elements.item(i);
            element.style.backgroundColor = "transparent";
            element.classList.remove("CBEditorWidgetSelected");
        }

        var index = args.array.indexOf(args.spec);

        /**
         * If the user clicked on the remove button the remove handler will
         * be executed first followed by this handler because of the click. In
         * that case, the element will no longer be in the DOM when this code
         * executes.
         */
        if (index > -1) {
            element     = args.parentElement.children.item(index);
            element.classList.add("CBEditorWidgetSelected");
            element.style.backgroundColor = "hsl(210, 100%, 97%)";
        }
    },

    /**
     * @param   {Array}     array
     * @param   {function}  handleChanged
     * @param   {Element}   parentElement
     * @param   {Object}    spec
     *
     * @return  undefined
     */
    createWidgetForSpec : function(args) {
        var handleInsert        = CBSpecArrayEditorFactory.handleInsert.bind(undefined, {
            array               : args.array,
            handleArrayChanged  : args.handleChanged,
            parentElement       : args.parentElement,
            spec                : args.spec
        });
        var handleMoveDown      = CBSpecArrayEditorFactory.handleMoveDown.bind(undefined, {
            array               : args.array,
            handleArrayChanged  : args.handleChanged,
            parentElement       : args.parentElement,
            spec                : args.spec
        });
        var handleMoveUp        = CBSpecArrayEditorFactory.handleMoveUp.bind(undefined, {
            array               : args.array,
            handleArrayChanged  : args.handleChanged,
            parentElement       : args.parentElement,
            spec                : args.spec
        });
        var handleRemove        = CBSpecArrayEditorFactory.handleRemove.bind(undefined, {
            array               : args.array,
            handleArrayChanged  : args.handleChanged,
            parentElement       : args.parentElement,
            spec                : args.spec
        });
        var handleSelect        = CBSpecArrayEditorFactory.handleSelect.bind(undefined, {
            array               : args.array,
            parentElement       : args.parentElement,
            spec                : args.spec
        });

        return CBEditorWidgetFactory.createWidget({
            handleInsert        : handleInsert,
            handleMoveDown      : handleMoveDown,
            handleMoveUp        : handleMoveUp,
            handleRemove        : handleRemove,
            handleSelect        : handleSelect,
            handleSpecChanged   : args.handleChanged,
            spec                : args.spec
        });
    }
};
