"use strict";
/* jshint strict: global */
/* exported CBPagesPreferencesEditor */
/* global
    CBUI,
    CBUIStringEditor,
*/

var CBPagesPreferencesEditor = {

    /**
     * @param object args
     *
     *      {
     *          spec: object
     *          specChangedCallback: function
     *      }
     *
     * @return Element
     */
    CBUISpecEditor_createEditorElement: function(args) {
        var section, item;
        var element = document.createElement("div");
        element.className = "CBPagesPreferencesEditor";

        element.appendChild(CBUI.createHalfSpace());

        var properties = [
            { name: "supportedViewClassNames", labelText: "Supported View Class Names" },
            { name: "deprecatedViewClassNames", labelText: "Deprecated View Class Name" },
            { name: "classNamesForLayouts", labelText: "Class Names for Layouts" },
        ];

        section = CBUI.createSection();

        properties.forEach(function (property) {
            item = CBUI.createSectionItem();
            item.appendChild(CBUIStringEditor.createEditor({
                labelText: property.labelText,
                propertyName: property.name,
                spec: args.spec,
                specChangedCallback: args.specChangedCallback,
            }).element);
            section.appendChild(item);
        });

        element.appendChild(section);
        element.appendChild(CBUI.createHalfSpace());

        return element;
    },
    /* CBUISpecEditor_createEditorElement() */

};
