"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported SCStripePreferencesEditor */
/* global
    CBConvert,
    CBModel,
    CBUI,
    CBUIBooleanSwitchPart,
    CBUIStringEditor,
*/

var SCStripePreferencesEditor = {

    /* -- CBUISpecEditor interfaces -- -- -- -- -- */



    /**
     * @param args object
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
        let elements;
        let spec = args.spec;

        if (typeof spec !== "object") {
            throw new Error(
                CBConvert.stringToCleanLine(`

                    The spec argument to
                    SCStripePreferencesEditor.createEditor() is not an object.

                `)
            );
        }

        let change = args.specChangedCallback;

        if (typeof change !== "function") {
            change = function () {};
        }

        elements = CBUI.createElementTree(
            "SCStripePreferencesEditor",
            "CBUI_title1"
        );

        let element = elements[0];

        {
            let titleElement = elements[1];
            titleElement.textContent = "API Keys";

            elements = CBUI.createElementTree(
                "CBUI_sectionContainer",
                "CBUI_section"
            );

            element.appendChild(
                elements[0]
            );

            let sectionElement = elements[1];

            {
                let stringEditor = CBUIStringEditor.create();
                stringEditor.title = "Live Publishable Key";

                stringEditor.value = CBModel.valueToString(
                    spec,
                    'livePublishableKey'
                );

                stringEditor.changed = function () {
                    spec.livePublishableKey = stringEditor.value;
                    change();
                };

                sectionElement.appendChild(
                    stringEditor.element
                );
            }

            {
                let stringEditor = CBUIStringEditor.create();
                stringEditor.title = "Live Secret Key";

                stringEditor.value = CBModel.valueToString(
                    spec,
                    'liveSecretKey'
                );

                stringEditor.changed = function () {
                    spec.liveSecretKey = stringEditor.value;
                    change();
                };

                sectionElement.appendChild(
                    stringEditor.element
                );
            }

            {
                let stringEditor = CBUIStringEditor.create();
                stringEditor.title = "Test Publishable Key";

                stringEditor.value = CBModel.valueToString(
                    spec,
                    'testPublishableKey'
                );

                stringEditor.changed = function () {
                    spec.testPublishableKey = stringEditor.value;
                    change();
                };

                sectionElement.appendChild(
                    stringEditor.element
                );
            }

            {
                let stringEditor = CBUIStringEditor.create();
                stringEditor.title = "Test Secret Key";

                stringEditor.value = CBModel.valueToString(
                    spec,
                    'testSecretKey'
                );

                stringEditor.changed = function () {
                    spec.testSecretKey = stringEditor.value;
                    change();
                };

                sectionElement.appendChild(
                    stringEditor.element
                );
            }
        }

        {
            elements = CBUI.createElementTree(
                "CBUI_sectionContainer",
                "CBUI_section",
                "CBUI_sectionItem",
                "CBUI_container_topAndBottom CBUI_flexGrow",
                ""
            );

            element.appendChild(
                elements[0]
            );

            let sectionItemElement = elements[2];
            let textElement = elements[4];
            textElement.textContent = "Enable Payments";

            let booleanSwitchPart = CBUIBooleanSwitchPart.create();

            booleanSwitchPart.value = CBModel.valueToBool(
                spec,
                'paymentsEnabled'
            );

            booleanSwitchPart.changed = function () {
                spec.paymentsEnabled = booleanSwitchPart.value;
                change();
            };

            sectionItemElement.appendChild(
                booleanSwitchPart.element
            );
        }

        return element;
    }
    /* CBUISpecEditor_createEditorElement() */

};
