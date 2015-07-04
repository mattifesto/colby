"use strict";

var CBTextBoxViewThemeEditorFactory = {

    /**
     * @param   {function}  handleSpecChanged
     * @param   {Object}    spec
     *
     * @return  {Element}
     */
    createEditor : function(args) {
        var element         = document.createElement("div");
        element.className   = "CBTextBoxViewThemeEditor";

        element.appendChild(CBStringEditorFactory.createSingleLineEditor({
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Title",
            propertyName        : "title",
            spec                : args.spec
        }));

        var CSSClass                = "T" + args.spec.ID;
        var classIndicator          = document.createElement("div");
        classIndicator.textContent  = "Theme CSS Class: " + CSSClass;

        element.appendChild(classIndicator);

        element.appendChild(CBStringEditorFactory.createMultiLineEditor({
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Styles",
            propertyName        : "styles",
            spec                : args.spec
        }));

        return element;
    }
};
