"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBKeyValuePairEditor */
/* global
    CBUI,
    CBUIStringEditor,
*/

var CBKeyValuePairEditor = {

    /**
     * @param object args.spec
     * @param object args.specChangedCallback
     *
     * @return Element
     */
    CBUISpecEditor_createEditorElement: function (args) {
        var item;
        var element = document.createElement("div");
        element.className = "CBKeyValuePairEditor";

        element.appendChild(CBUI.createHalfSpace());

        var section = CBUI.createSection();

        /* key */
        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText: "Key",
            propertyName: "key",
            spec: args.spec,
            specChangedCallback: args.specChangedCallback,
        }).element);
        section.appendChild(item);

        /* value */
        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
            labelText: "Value As JSON",
            propertyName: "valueAsJSON",
            spec: args.spec,
            specChangedCallback: args.specChangedCallback,
        }).element);
        section.appendChild(item);
        element.appendChild(section);

        element.appendChild(CBUI.createHalfSpace());

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
