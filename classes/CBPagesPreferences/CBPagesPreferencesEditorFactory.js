"use strict";

var CBPagesPreferencesEditorFactory = {

    /**
     * @param {function}    handleSpecChanged
     * @param {Object}      spec
     *
     * @return Element
     */
    createEditor : function(args) {
        var element         = document.createElement("div");
        element.className   = "CBPagesPreferencesEditor";
        var h1              = document.createElement("h1");
        h1.textContent      = "Pages Preferences";

        element.appendChild(h1);

        element.appendChild(CBStringEditorFactory.createMultiLineEditor({
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Supported View Class Names",
            propertyName        : "supportedViewClassNames",
            spec                : args.spec
        }));

        element.appendChild(CBStringEditorFactory.createMultiLineEditor({
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Deprecated View Class Names",
            propertyName        : "deprecatedViewClassNames",
            spec                : args.spec
        }));

        return element;
    }
};
