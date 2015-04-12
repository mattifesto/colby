"use strict";

var CBViewEditorWidgetFactory = {

    /**
     * @param spec
     * @param handleModelChanged
     * @param handleViewDeleted
     *
     * @return element
     */
    widgetForSpec : function(args) {
        /* deprecated code just to make this work at first*/

        var viewEditor              = CBViewEditor.editorForViewModel(args.spec);
        viewEditor.deleteCallback   = args.handleViewDeleted;

        return viewEditor.outerElement();
    }
};
