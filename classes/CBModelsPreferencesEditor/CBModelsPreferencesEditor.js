"use strict";
/* jshint strict: global */
/* exported CBModelsPreferencesEditor */
/* global
    CBUI,
    CBUIStringEditor */

var CBModelsPreferencesEditor = {

    /**
     * @param object args.spec
     * @param function args.specChangedCallback
     *
     * @return Element
     */
    CBUISpecEditor_createEditorElement: function(args) {
        var element = document.createElement("section");
        element.className = "CBModelsPreferencesEditor";

        element.appendChild(CBUI.createHalfSpace());

        var section = CBUI.createSection();

        /* classNamesOfEditableModels */
        var item = CBUI.createSectionItem();

        item.appendChild(CBUIStringEditor.createEditor({
            labelText: "Class Names of Editable Models",
            propertyName: "classNamesOfEditableModels",
            spec: args.spec,
            specChangedCallback: args.specChangedCallback,
        }).element);
        section.appendChild(item);
        element.appendChild(section);

        element.appendChild(CBUI.createHalfSpace());

        return element;
    },
    /* CBUISpecEditor_createEditorElement() */

};
