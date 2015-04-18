"use strict";

var CBViewEditorWidgetFactory = {

    /**
     * @param handleSpecChanged
     * @param handleViewDeleted
     * @param spec
     *
     * @return Element
     */
    createWidget : function(args) {
        var factory;

        if ((factory = window[args.spec.className + "EditorFactory"])) {
            var editorElement       = factory.createEditor({
                handleSpecChanged   : CBPageEditor.requestSave.bind(CBPageEditor),
                spec                : args.spec });

            var chromeElement       = CBViewEditorChromeFactory.createChrome({
                editorElement       : editorElement,
                handleViewDeleted   : args.handleViewDeleted,
                title               : args.spec.className });

            if (typeof factory.widgetClassName == "function") {
                chromeElement.className = factory.widgetClassName();
            } else {
                chromeElement.className = "CBViewEditorWidget";
            }

            return chromeElement;
        } else {
            var viewEditor              = CBViewEditor.editorForViewModel(args.spec);
            viewEditor.deleteCallback   = args.handleViewDeleted;

            return viewEditor.outerElement();
        }
    }
};
