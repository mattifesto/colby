"use strict";

var CBSitePreferencesEditorFactory = {

    /**
     * @param {function}    handleSpecChanged
     * @param {Object}      spec
     *
     * @return Element
     */
    createEditor : function(args) {
        var element         = document.createElement("div");
        element.className   = "CBSitePreferencesEditor";

        element.appendChild(CBBooleanEditorFactory.createCheckboxEditor({
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Debug",
            propertyName        : "debug",
            spec                : args.spec
        }));

        element.appendChild(CBStringEditorFactory.createSingleLineEditor({
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Google Tag Manager ID",
            propertyName        : "googleTagManagerID",
            spec                : args.spec
        }));

        return element;
    }
};
