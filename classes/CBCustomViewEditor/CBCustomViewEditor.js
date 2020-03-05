"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBCustomViewEditor */
/* global
    CBModel,
    CBUI,
    CBUIStringEditor,
*/



var CBCustomViewEditor = {

    /* -- CBUISpecEditor interfaces -- -- -- -- -- */



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
    CBUISpecEditor_createEditorElement(
        args
    ) {
        let spec = args.spec;
        let specChangedCallback = args.specChangedCallback;

        let elements = CBUI.createElementTree(
            "CBCustomViewEditor",
            "CBUI_sectionContainer",
            "CBUI_section"
        );

        let element = elements[0];
        let sectionElement = elements[2];


        /* custom view class name */
        {
            let classNameEditor = CBUIStringEditor.create();
            classNameEditor.title = "Custom View Class Name";

            classNameEditor.value = CBModel.valueToString(
                spec,
                "customViewClassName"
            );

            classNameEditor.changed = function () {
                spec.customViewClassName = classNameEditor.value;
                specChangedCallback();
            };

            sectionElement.appendChild(
                classNameEditor.element
            );
        }
        /* custom view class name */


        /* custom properties */

        if (typeof spec.properties !== "object") {
            spec.properties = {};
        }

        let propertiesAsJSON = JSON.stringify(
            spec.properties,
            undefined,
            2
        );

        elements = CBUI.createElementTree(
            "CBUI_sectionContainer",
            "CBUI_section"
        );

        element.appendChild(
            elements[0]
        );

        sectionElement = elements[1];

        let propertiesAsJSONEditor = CBUIStringEditor.create();
        propertiesAsJSONEditor.title = "Custom Properties";

        propertiesAsJSONEditor.value = propertiesAsJSON;

        propertiesAsJSONEditor.changed = function () {
            let propertiesObject;
            let currentPropertiesAsJSON = propertiesAsJSONEditor.value;

            try {
                propertiesObject = JSON.parse(
                    currentPropertiesAsJSON
                );
            } catch (error) {
                propertiesAsJSONEditor.element.style.backgroundColor = (
                    "hsl(0, 100%, 90%)"
                );

                return;
            }

            if (typeof propertiesObject !== "object") {
                propertiesAsJSONEditor.element.style.backgroundColor = (
                    "hsl(0, 100%, 90%)"
                );

                return;
            }

            propertiesAsJSONEditor.element.style.backgroundColor = "white";
            spec.properties = propertiesObject;
            specChangedCallback();
        };

        sectionElement.appendChild(
            propertiesAsJSONEditor.element
        );

        return element;
    },
    /* CBUISpecEditor_createEditorElement() */



    /* -- CBUISpec interfaces -- -- -- -- -- */



    /**
     * @param string? spec.customViewClassName
     *
     * @return string|undefined
     */
    CBUISpec_toDescription: function (spec) {
        let description =
        CBModel.valueToString(spec, "customViewClassName") ||
        CBModel.valueToString(spec, "properties.className") ||
        undefined;

        return description;
    },
    /* CBUISpec_toDescription() */

};
/* CBCustomViewEditor */
