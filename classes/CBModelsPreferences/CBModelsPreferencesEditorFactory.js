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

        if (!args.spec.classMenuItems) {
            args.spec.classMenuItems = [];
        }

        element.appendChild(CBSpecArrayEditorFactory.createEditor({
            array           : args.spec.classMenuItems,
            classNames      : ["CBClassMenuItem"],
            handleChanged   : args.handleSpecChanged
        }));

        return element;
    }
};
