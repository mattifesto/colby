"use strict";

var CBMenuItemEditorFactory = {

    /**
     * @param   {function}  handleSpecChanged
     * @param   {Object}    spec
     *
     * @return  {Element}
     */
    createEditor : function(args) {
        var element         = document.createElement("div");
        element.className   = "CBMenuItemEditor";

        element.appendChild(CBStringEditorFactory.createSingleLineEditor({
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Name",
            propertyName        : "name",
            spec                : args.spec
        }));

        element.appendChild(CBStringEditorFactory.createSingleLineEditor({
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Text",
            propertyName        : "text",
            spec                : args.spec
        }));

        element.appendChild(CBStringEditorFactory.createSingleLineEditor({
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "URL",
            propertyName        : "URL",
            spec                : args.spec
        }));

        return element;
    }
};
