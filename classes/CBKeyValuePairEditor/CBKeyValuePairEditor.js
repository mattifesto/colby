"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBKeyValuePairEditor */
/* global
    CBUI,
    CBUIStringEditor2,
*/

var CBKeyValuePairEditor = {

    /**
     * @param object args.spec
     * @param object args.specChangedCallback
     *
     * @return Element
     */
    CBUISpecEditor_createEditorElement: function (args) {
        let spec = args.spec;
        let specChangedCallback = args.specChangedCallback;

        let elements = CBUI.createElementTree(
            "CBKeyValuePairEditor",
            "CBUI_sectionContainer",
            "CBUI_section"
        );

        let element = elements[0];
        let sectionElement = elements[2];

        /* key */
        sectionElement.appendChild(
            CBUIStringEditor2.createObjectPropertyEditorElement(
                spec,
                "key",
                "Key",
                specChangedCallback
            )
        );

        /* value */
        sectionElement.appendChild(
            CBUIStringEditor2.createObjectPropertyEditorElement(
                spec,
                "valueAsJSON",
                "Value As JSON",
                specChangedCallback
            )
        );

        return element;
    },
    /* CBUISpecEditor_createEditorElement() */



    /**
     * @param string? spec.key
     * @param string? spec.valueAsJSON
     *
     * @return string|undefined
     */
    CBUISpec_toDescription: function (spec) {
        if (spec.key === undefined) {
            return undefined;
        } else {
            return spec.key + ": " + spec.valueAsJSON;
        }
    },
    /* CBUISpec_toDescription() */
};
