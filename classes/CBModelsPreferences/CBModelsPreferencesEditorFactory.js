"use strict";

var CBModelsPreferencesEditorFactory = {

    /**
     * @param {function}    handleSpecChanged
     * @param {Object}      spec
     *
     * @return Element
     */
    createEditor : function(args) {
        var element         = document.createElement("div");
        element.className   = "CBModelsPreferencesEditor";
        var h1              = document.createElement("h1");
        h1.textContent      = "Models Preferences";
        var container       = document.createElement("div");

        element.appendChild(h1);

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
