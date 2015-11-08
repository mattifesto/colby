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
            labelText           : "Editable Model Classes",
            propertyName        : "editableModelClasses",
            spec                : args.spec
        }));

        section.appendChild(item);
        element.appendChild(section);

        element.appendChild(CBModelsPreferencesEditorFactory.createHalfSpaceElement());

        var container       = document.createElement("div");

        if (!args.spec.classMenuItems) {
            args.spec.classMenuItems = [];
        }

        var classMenuItemsEditor = CBSpecArrayEditorFactory.createEditor({
            array           : args.spec.classMenuItems,
            classNames      : ["CBClassMenuItem"],
            handleChanged   : args.handleSpecChanged
        });

        element.appendChild(CBModelsPreferencesEditorFactory.createSection({
            contentElement  : classMenuItemsEditor,
            title           : 'Class Menu Items'
        }));

        return element;
    },

    createHalfSpaceElement : function() {
        var element = document.createElement("div");
        element.className = "CBUIHalfSpace";

        return element;
    },

    /**
     * @param   {Element}   contentElement
     * @param   {string}    title
     *
     * @return  {Element}
     */
    createSection : function(args) {
        var section     = document.createElement("section");
        var h1          = document.createElement("h1");
        h1.textContent  = args.title || '';
        var container   = document.createElement("div");

        container.appendChild(args.contentElement);
        section.appendChild(h1);
        section.appendChild(container);

        return section;
    }
};
