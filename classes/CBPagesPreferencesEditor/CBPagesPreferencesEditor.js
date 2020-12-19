"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBPagesPreferencesEditor */
/* global
    CBUI,
    CBUIStringEditor2,
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
    CBUISpecEditor_createEditorElement: function(
        args
    ) {
        let spec = args.spec;
        let specChangedCallback = args.specChangedCallback;
        let element;
        let sectionElement;

        {
            let elements = CBUI.createElementTree(
                "CBPagesPreferencesEditor",
                "CBUI_sectionContainer",
                "CBUI_section"
            );

            element = elements[0];
            sectionElement = elements[2];
        }

        let properties = [
            {
                name: "supportedViewClassNames",
                labelText: "Supported View Class Names"
            },
            {
                name: "deprecatedViewClassNames",
                labelText: "Deprecated View Class Name"
            },
            {
                name: "classNamesForLayouts",
                labelText: "Class Names for Layouts"
            },
        ];

        properties.forEach(
            function (
                property
            ) {
                sectionElement.appendChild(
                    CBUIStringEditor2.createObjectPropertyEditorElement(
                        spec,
                        property.name,
                        property.labelText,
                        specChangedCallback
                    )
                );
            }
        );

        return element;
    },
    /* CBUISpecEditor_createEditorElement() */

};
