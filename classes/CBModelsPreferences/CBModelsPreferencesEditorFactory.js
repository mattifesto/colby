"use strict";

var CBModelsPreferencesEditorFactory = {

    /**
     * @param {function}    handleSpecChanged
     * @param {Object}      spec
     *
     * @return Element
     */
    createEditor : function(args) {
        var element         = document.createElement("section");
        element.className   = "CBModelsPreferencesEditor";

        element.appendChild(CBModelsPreferencesEditorFactory.createHalfSpaceElement());

        var section = document.createElement("div");
        section.className = "CBUISection";

        var item = document.createElement("div");
        item.className = "CBUISectionItem";

        item.appendChild(CBResponsiveEditorFactory.createStringEditorWithTextArea({
            handleSpecChanged   : args.handleSpecChanged,
            labelText           : "Class Names of Editable Models",
            propertyName        : "classNamesOfEditableModels",
            spec                : args.spec
        }));

        section.appendChild(item);
        element.appendChild(section);

        element.appendChild(CBModelsPreferencesEditorFactory.createHalfSpaceElement());

        return element;
    },

    /**
     * @return {Element}
     */
    createHalfSpaceElement : function() {
        var element = document.createElement("div");
        element.className = "CBUIHalfSpace";

        return element;
    },
};
