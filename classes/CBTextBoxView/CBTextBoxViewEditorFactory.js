"use strict";

var CBTextBoxViewEditorFactory = {

    /**
     * @param   {function}  handleSpecChanged
     * @param   {Object}    spec
     *
     * @return  {Element}
     */
    createEditor : function(args) {
        var element         = document.createElement("div");
        element.className   = "CBTextBoxViewEditor";

        element.appendChild(CBStringEditorFactory.createSingleLineEditor({
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Title",
            propertyName        : "title",
            spec                : args.spec
        }));

        element.appendChild(CBStringEditorFactory.createMultiLineEditor({
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Content",
            propertyName        : "contentAsMarkaround",
            spec                : args.spec
        }));

        return element;
    }
};
