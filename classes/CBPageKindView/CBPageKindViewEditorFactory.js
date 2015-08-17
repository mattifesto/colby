"use strict";

var CBPageKindViewEditorFactory = {

    /**
     * @param {function}    handleSpecChanged
     * @param {Object}      spec
     *
     * @return {Element}
     */
    createEditor : function(args) {
        var editor          = document.createElement("div");
        editor.className    = "CBPageKindViewEditor";

        editor.appendChild(CBStringEditorFactory.createSingleLineEditor({
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Class Name for Kind",
            propertyName        : "classNameForKind",
            spec                : args.spec
        }));

        return editor;
    }
};
