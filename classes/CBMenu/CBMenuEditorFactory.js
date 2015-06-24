"use strict";

var CBMenuEditorFactory = {

    /**
     * @param   {function}  handleSpecChanged
     * @param   {Object}    spec
     *
     * @return  {Element}
     */
    createEditor : function(args) {
        var element         = document.createElement("div");
        element.className   = "CBMenuEditor";

        element.appendChild(CBStringEditorFactory.createSingleLineEditor({
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Title",
            propertyName        : "title",
            spec                : args.spec
        }));

        if (!args.spec.items) {
            args.spec.items = [];
        }

        element.appendChild(CBSpecArrayEditorFactory.createEditor({
            array           : args.spec.items,
            classNames      : ["CBMenuItem"],
            handleChanged   : args.handleSpecChanged
        }));

        return element;
    }
};
