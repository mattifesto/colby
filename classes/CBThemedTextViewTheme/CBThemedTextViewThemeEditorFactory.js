"use strict";

var CBThemedTextViewThemeEditorFactory = {

    /**
     * @param   {function}  handleSpecChanged
     * @param   {Object}    spec
     *
     * @return  {Element}
     */
    createEditor : function(args) {
        return CBThemeEditorFactory.createEditor(args);
    },
};
