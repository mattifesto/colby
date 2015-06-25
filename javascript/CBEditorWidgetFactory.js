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
        var widgetElement       = document.createElement("div");
        widgetElement.className = "CBEditorWidget";

        // Create toolbar

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

        if (args.handleSelect) {
            widgetElement.addEventListener("click", args.handleSelect);
            widgetElement.addEventListener("focusin", args.handleSelect);
        }

        var editorFactory   = window[args.spec.className + "EditorFactory"] || CBEditorWidgetFactory;
        var editor          = editorFactory.createEditor(args);

        widgetElement.appendChild(toolbar);
        widgetElement.appendChild(editor);

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
    }
};
