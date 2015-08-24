"use strict";

var CBPageKindLibraryViewEditorFactory = {

    /**
     * @param {function}    handleSpecChanged
     * @param {Object}      spec
     *
     * @return {Element}
     */
    createEditor : function(args) {
        var editor          = document.createElement("div");
        editor.className    = "CBPageKindLibraryViewEditor";

        editor.appendChild(CBStringEditorFactory.createSingleLineEditor({
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Class Name for Kind",
            propertyName        : "classNameForKind",
            spec                : args.spec
        }));

        editor.appendChild(CBTextBoxViewEditorFactory.createThemeIDEditor({
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Header Theme",
            propertyName        : "headerThemeID",
            spec                : args.spec
        }));

        editor.appendChild(CBTextBoxViewEditorFactory.createThemeIDEditor({
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Summary Theme",
            propertyName        : "summaryThemeID",
            spec                : args.spec
        }));

        editor.appendChild(CBTextBoxViewEditorFactory.createThemeIDEditor({
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Year Theme",
            propertyName        : "yearThemeID",
            spec                : args.spec
        }));

        return editor;
    }
};
