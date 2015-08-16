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

        element.appendChild(CBStringEditorFactory.createMultiLineEditor({
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Class Names for Kinds",
            propertyName        : "classNamesForKinds",
            spec                : args.spec
        }));

        return element;
    }
};
