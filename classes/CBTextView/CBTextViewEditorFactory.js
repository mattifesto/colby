"use strict";

var CBTextViewEditorFactory = {

    /**
     * @param labelText
     * @param spec
     *
     * @return Element
     */
    createEditor : function(args) {
        var deprecatedViewEditor        = CBViewEditor.editorForViewModel(args.spec);
        deprecatedViewEditor.labelText  = args.labelText;

        return deprecatedViewEditor.element();
    }
};
