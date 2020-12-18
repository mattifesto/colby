"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBModelsPreferencesEditor */
/* global
    CBUI,
    CBUIStringEditor2
*/


var CBModelsPreferencesEditor = {

    /**
     * @param object args.spec
     * @param function args.specChangedCallback
     *
     * @return Element
     */
    CBUISpecEditor_createEditorElement: function(
        args
    ) {
        let spec = args.spec;
        let specChangedCallback = args.specChangedCallback;

        let elements = CBUI.createElementTree(
            "CBModelsPreferencesEditor",
            "CBUI_sectionContainer",
            "CBUI_section"
        );

        let element = elements[0];
        let sectionElement =  elements[2];

        /* classNamesOfEditableModels */

        sectionElement.appendChild(
            CBUIStringEditor2.createObjectPropertyEditorElement(
                spec,
                "classNamesOfEditableModels",
                "Class Names of Editable Models",
                specChangedCallback
            )
        );

        return element;
    },
    /* CBUISpecEditor_createEditorElement() */

};
