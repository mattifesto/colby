"use strict";

var CBThemedMenuViewThemeEditorFactory = {

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
