"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* global
    CBUI,
    CBUIBooleanSwitchPart,
    CBUIStringEditor,
    Colby,
*/



Colby.afterDOMContentLoaded(
    function afterDOMContentLoaded() {
        let mainElement = document.getElementsByTagName("main")[0];

        mainElement.appendChild(
            createBooleanSwitchElement()
        );

        mainElement.appendChild(
            createStringEditorElement()
        );

        return;



        /* -- closures -- -- -- -- -- */



        /**
         * @return Element
         */
        function createBooleanSwitchElement() {
            let elements = CBUI.createElementTree(
                "BooleanSwitchElement",
                "CBUI_title1"
            );

            let element = elements[0];

            elements[1].textContent = "CBUIBooleanSwitchPart";


            /* CBDarkTheme */

            elements = CBUI.createElementTree(
                "CBUI_sectionContainer",
                "CBUI_section",
                "CBUI_sectionItem",
                "CBUI_container_topAndBottom CBUI_flexGrow",
                "text"
            );

            element.appendChild(
                elements[0]
            );

            elements[4].textContent = "CBDarkTheme";

            let toggle = CBUIBooleanSwitchPart.create();

            elements[2].appendChild(
                toggle.element
            );

            toggle.changed = function () {
                if (toggle.value === true) {
                    mainElement.classList.add("CBDarkTheme");
                } else {
                    mainElement.classList.remove("CBDarkTheme");
                }
            };


            /* Example */

            elements = CBUI.createElementTree(
                "CBUI_sectionContainer",
                "CBUI_section",
                "CBUI_sectionItem",
                "CBUI_container_topAndBottom CBUI_flexGrow",
                "text"
            );

            element.appendChild(
                elements[0]
            );

            elements[4].textContent = "Example";

            elements[2].appendChild(
                CBUIBooleanSwitchPart.create().element
            );

            return element;
        }
        /* createBooleanSwitchElement() */



        /**
         * @return Element
         */
        function createStringEditorElement() {
            let elements = CBUI.createElementTree(
                "BooleanSwitchElement",
                "CBUI_title1"
            );

            let element = elements[0];

            elements[1].textContent = "CBUIStringEditor";


            /* CBUIStringEditor */

            elements = CBUI.createElementTree(
                "CBUI_sectionContainer",
                "CBUI_section"
            );

            element.appendChild(
                elements[0]
            );

            let stringEditor = CBUIStringEditor.create();
            stringEditor.title = "name";
            stringEditor.value = "Bob Jones";

            elements[1].appendChild(
                stringEditor.element
            );

            return element;
        }
        /* createStringEditorElement() */

    }
    /* afterDOMContentLoaded() */
);
