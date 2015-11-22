"use strict";

var CBSpecArrayEditorFactory = {

    /**
     * @param   [Object] array
     * @param   [string] classNames
     * @param   function handleChanged
     *
     * @return  Element
     */
    createEditor : function(args) {
        var element         = document.createElement("div");
        element.className   = "CBSpecArrayEditor";
        var container       = document.createElement("div");
        container.className = "container";

        args.array.forEach(function(spec) {
            var widgetElement = CBSpecArrayEditorFactory.createWidgetForSpec({
                array           : args.array,
                classNames      : args.classNames,
                handleChanged   : args.handleChanged,
                parentElement   : container,
                spec            : spec
            })
            container.appendChild(widgetElement);
        });

        var footer          = document.createElement("div");
        footer.className    = "footer";
        var menu            = document.createElement("select");
        var append          = document.createElement("button");
        append.textContent  = "Append";

        append.addEventListener("click", CBSpecArrayEditorFactory.handleAppend.bind(undefined, {
            array               : args.array,
            classNames          : args.classNames,
            handleArrayChanged  : args.handleChanged,
            parentElement       : container,
            selectElement       : menu
        }));

        args.classNames.forEach(function(className) {
            var option          = document.createElement("option");
            option.textContent  = className;
            option.value        = className;
            menu.appendChild(option);
        });

        var paste           = document.createElement("button");
        paste.textContent   = "Paste";

        paste.addEventListener("click", CBSpecArrayEditorFactory.handlePaste.bind(undefined, {
            array                   : args.array,
            beforeSpec              : null,
            selectableClassNames    : args.classNames,
            handleArrayChanged      : args.handleChanged,
            parentElement           : container
        }));

        footer.appendChild(menu);
        footer.appendChild(append);
        footer.appendChild(paste);
        element.appendChild(container);
        element.appendChild(footer);

        return element;
    },

    /**
     * @param   {Array}     array
     * @param   {Array}     classNames
     * @param   {function}  handleChanged
     * @param   {Element}   parentElement
     * @param   {Object}    spec
     *
     * @return  undefined
     */
    createWidgetForSpec : function(args) {
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

        var copy            = document.createElement("button");
        copy.textContent    = "C";

        copy.addEventListener("click", CBSpecArrayEditorFactory.handleCopy.bind(undefined, {
            spec : args.spec
        }));

        var paste           = document.createElement("button");
        paste.textContent   = "P";

        paste.addEventListener("click", CBSpecArrayEditorFactory.handlePaste.bind(undefined, {
            array                   : args.array,
            beforeSpec              : args.spec,
            selectableClassNames    : args.classNames,
            handleArrayChanged      : args.handleChanged,
            parentElement           : args.parentElement
        }));

        var menu = document.createElement("select");

        args.classNames.forEach(function(className) {
            var option          = document.createElement("option");
            option.textContent  = className;
            option.value        = className;
            menu.appendChild(option);
        });

        var insert          = document.createElement("button");
        insert.textContent  = "Insert";

        insert.addEventListener("click", CBSpecArrayEditorFactory.handleInsert.bind(undefined, {
            array               : args.array,
            classNames          : args.classNames,
            handleArrayChanged  : args.handleChanged,
            parentElement       : args.parentElement,
            selectElement       : menu,
            spec                : args.spec
        }));

        return CBEditorWidgetFactory.createWidget({
            handleMoveDown      : handleMoveDown,
            handleMoveUp        : handleMoveUp,
            handleRemove        : handleRemove,
            handleSpecChanged   : args.handleChanged,
            spec                : args.spec,
            toolbarElements     : [copy, paste, menu, insert]
        });
    },

    /**
     * @param   {Array}     array
     * @param   {Array}     classNames
     * @param   {function}  handleArrayChanged
     * @param   {Element}   parentElement
     * @param   {Element}   selectElement
     *
     * @return  undefined
     */
    handleAppend : function(args) {
        var spec            = {
            className       : args.selectElement.value
        };
        var widgetElement   = CBSpecArrayEditorFactory.createWidgetForSpec({
            array           : args.array,
            classNames      : args.classNames,
            handleChanged   : args.handleArrayChanged,
            parentElement   : args.parentElement,
            spec            : spec
        });

        args.array.push(spec);
        args.parentElement.appendChild(widgetElement);

        args.handleArrayChanged.call();
    },

    /**
     * @param   {Object}    spec
     *
     * @return  undefined
     */
    handleCopy : function(args) {
        var spec = JSON.stringify(args.spec);
        localStorage.setItem("specClipboard", spec);
    },

    /**
     * @param   {Array}     array
     * @param   {Array}     classNames
     * @param   {function}  handleArrayChanged
     * @param   {Element}   parentElement
     * @param   {Element}   selectElement
     * @param   {Object}    spec
     *
     * @return  undefined
     */
    handleInsert : function(args) {
        var index           = args.array.indexOf(args.spec);
        var beforeElement   = args.parentElement.children.item(index);
        var spec            = {
            className       : args.selectElement.value
        };
        var widgetElement   = CBSpecArrayEditorFactory.createWidgetForSpec({
            array           : args.array,
            classNames      : args.classNames,
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
     * @param   {Object}    beforeSpec
     * @param   {function}  handleArrayChanged
     * @param   {Element}   parentElement
     * @param   {Array}     selectableClassNames
     *
     * @return  undefined
     */
    handlePaste : function(args) {
        var spec            = localStorage.getItem("specClipboard");

        if (spec === null) {
            return;
        }

        spec                = JSON.parse(spec);
        var widgetElement   = CBSpecArrayEditorFactory.createWidgetForSpec({
            array           : args.array,
            classNames      : args.selectableClassNames,
            handleChanged   : args.handleArrayChanged,
            parentElement   : args.parentElement,
            spec            : spec
        });

        if (args.beforeSpec) {
            var index           = args.array.indexOf(args.beforeSpec);
            var beforeElement   = args.parentElement.children.item(index);
            args.array.splice(index, 0, spec); // insert before spec at index
            args.parentElement.insertBefore(widgetElement, beforeElement);
        } else {
            args.array.push(spec);
            args.parentElement.appendChild(widgetElement);
        }

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
    handleRemove : function(args) {
        if (confirm("Are you sure you want to remove this item?")) {
            var index           = args.array.indexOf(args.spec);
            var widgetElement   = args.parentElement.children.item(index);

            args.array.splice(index, 1); // remove at index

            args.parentElement.removeChild(widgetElement);

            args.handleArrayChanged.call();
        }
    }
};
