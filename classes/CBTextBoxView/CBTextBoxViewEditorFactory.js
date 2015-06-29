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
        var settings        = document.createElement("div");
        settings.className  = "settings";

        settings.appendChild(CBStringEditorFactory.createSelectEditor({
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Theme",
            propertyName        : "themeID",
            spec                : args.spec
        }));

        settings.appendChild(CBStringEditorFactory.createSingleLineEditor({
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Width",
            propertyName        : "width",
            spec                : args.spec
        }));

        settings.appendChild(CBStringEditorFactory.createSingleLineEditor({
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Height",
            propertyName        : "height",
            spec                : args.spec
        }));

        element.appendChild(settings);

        element.appendChild(CBStringEditorFactory.createSingleLineEditor({
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Title",
            propertyName        : "titleAsMarkaround",
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
