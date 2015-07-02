"use strict";

var CBEditorWidgetFactory = {

    /**
     * @param   {function}  handleMoveDown
     * @param   {function}  handleMoveUp
     * @param   {function}  handleRemove
     * @param   {function}  handleSpecChanged
     * @param   {Object}    spec
     * @param   {Array}     toolbarElements
     *
     * @return  {Element}
     */
    createWidget : function(args) {
        var widgetElement       = document.createElement("section");
        widgetElement.className = "CBEditorWidget";
        var container           = document.createElement("div");
        var title               = document.createElement("h1");
        title.textContent       = args.spec.className;
        var toolbar             = document.createElement("div");
        toolbar.className       = "toolbar";

        if (args.handleMoveDown) {
            var moveDownButton          = document.createElement("button");
            moveDownButton.textContent  = "Move Down";
            moveDownButton.addEventListener("click", args.handleMoveDown);
            toolbar.appendChild(moveDownButton);
        }

        if (args.handleMoveUp) {
            var moveUpButton            = document.createElement("button");
            moveUpButton.textContent    = "Move Up";
            moveUpButton.addEventListener("click", args.handleMoveUp);
            toolbar.appendChild(moveUpButton);
        }

        if (args.handleRemove) {
            var removeButton            = document.createElement("button");
            removeButton.textContent    = "Remove";
            removeButton.addEventListener("click", args.handleRemove);
            toolbar.appendChild(removeButton);
        }

        args.toolbarElements.forEach(function(toolbarElement) {
            toolbar.appendChild(toolbarElement);
        });

        var handleSelect     = CBEditorWidgetFactory.handleSelect.bind(undefined, {
            handleSelect    : args.handleSelect,
            widgetElement   : widgetElement
        });

        widgetElement.addEventListener("click", handleSelect);
        widgetElement.addEventListener("focusin", handleSelect);

        var editorFactory   = window[args.spec.className + "EditorFactory"] || CBEditorWidgetFactory;
        var editor          = editorFactory.createEditor(args);

        container.appendChild(title);
        container.appendChild(toolbar);
        container.appendChild(editor);
        widgetElement.appendChild(container);

        return widgetElement;
    },

    /**
     * This object also behaves as a default editor factory for specs where an
     * editor factory is not available.
     *
     * @param   {function}  handleSpecChanged
     * @param   {Object}    spec
     *
     * @return  {Element}
     */
    createEditor : function(args) {
        var element         = document.createElement("div");
        element.className   = "CBDefaultEditor";
        var pre             = document.createElement("pre");
        pre.textContent     = JSON.stringify(args.spec, null, 2);

        element.appendChild(pre);

        return element;
    },

    /**
     * When the user has clicked within or otherwise selected this element this
     * method is called to perform the selection.
     *
     * @param   {function}  handleSelect
     * @param   {Element}   widgetElement
     *
     * @return  undefined
     */
    handleSelect : function(args, event) {
        var elements    = document.getElementsByClassName("CBEditorWidgetSelected");
        var count       = elements.length;

        for (var i = 0; i < count; i++) {
            elements.item(i).classList.remove("CBEditorWidgetSelected");
        }

        args.widgetElement.classList.add("CBEditorWidgetSelected");

        if (args.handleSelect) {
            args.handleSelect.call();
        }

        event.stopPropagation();
    }
};
