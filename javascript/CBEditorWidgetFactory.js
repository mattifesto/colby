"use strict";

var CBEditorWidgetFactory = {

    /**
     * @param   {function}  handleInsert
     * @param   {function}  handleMoveDown
     * @param   {function}  handleMoveUp
     * @param   {function}  handleRemove
     * @param   {function}  handleSpecChanged
     * @param   {Object}    spec
     *
     * @return  {Element}
     */
    createWidget : function(args) {
        var widgetElement       = document.createElement("div");
        widgetElement.className = "CBEditorWidget";
        var menu                = document.createElement("div");
        menu.className          = "menu";

        if (args.handleMoveDown) {
            var moveDownButton          = document.createElement("button");
            moveDownButton.textContent  = "Move Down";
            moveDownButton.addEventListener("click", args.handleMoveDown);
            menu.appendChild(moveDownButton);
        }

        if (args.handleMoveUp) {
            var moveUpButton            = document.createElement("button");
            moveUpButton.textContent    = "Move Up";
            moveUpButton.addEventListener("click", args.handleMoveUp);
            menu.appendChild(moveUpButton);
        }

        if (args.handleInsert) {
            var insertButton            = document.createElement("button");
            insertButton.textContent    = "Insert";
            insertButton.addEventListener("click", args.handleInsert);
            menu.appendChild(insertButton);
        }

        if (args.handleRemove) {
            var removeButton            = document.createElement("button");
            removeButton.textContent    = "Remove";
            removeButton.addEventListener("click", args.handleRemove);
            menu.appendChild(removeButton);
        }

        if (args.handleSelect) {
            widgetElement.addEventListener("click", args.handleSelect);
            widgetElement.addEventListener("focusin", args.handleSelect);
        }

        widgetElement.appendChild(menu);
        widgetElement.appendChild(CBEditorWidgetFactory.createEditor(args));

        return widgetElement;
    },

    /**
     * @param   {function}  handleSpecChanged
     * @param   {Object}    spec
     *
     * @return  {Object}
     */
    createEditor : function(args) {
        var editorFactory;

        if (args.spec.className !== undefined && (editorFactory = window[args.spec.className + "EditorFactory"])) {
            editorFactory;
        } else {
            return undefined; // TODO: should be CBEditorFactory, the generic editor factory
        }

        return editorFactory.createEditor(args);
    }
};
