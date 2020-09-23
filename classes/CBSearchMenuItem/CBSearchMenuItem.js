"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBSearchMenuItem */
/* global
    CBUI,
    CBUIStringEditor,
    Colby,
*/

var CBSearchMenuItem = {

    /**
     * @param Element element
     *
     * @return undefined
     */
    activateElement: function (element) {
        element.addEventListener(
            "click",
            function CBSearchMenuItem_show() {
                let element = CBUI.createElement(
                    "CBUI_panel CBUIRoot",
                    "form"
                );

                element.action = "/search/";

                document.body.appendChild(element);

                let stringEditor = CBUIStringEditor.create(
                    {
                        inputType: "CBUIStringEditor_text",
                    }
                );

                stringEditor.name = "search-for";
                stringEditor.title = "Search For";

                {
                    let sectionContainerElement = CBUI.createElement(
                        "CBUI_sectionContainer"
                    );

                    element.appendChild(sectionContainerElement);

                    let sectionElement = CBUI.createElement("CBUI_section");

                    sectionContainerElement.appendChild(sectionElement);

                    sectionElement.appendChild(
                        stringEditor.element
                    );
                }

                {
                    let sectionContainerElement = CBUI.createElement(
                        "CBUI_container1"
                    );

                    element.appendChild(sectionContainerElement);

                    let buttonElement = CBUI.createElement("CBUI_button1");

                    sectionContainerElement.appendChild(buttonElement);

                    buttonElement.textContent = "Search";

                    buttonElement.addEventListener(
                        "click",
                        function CBSearchMenuItem_search() {
                            document.body.removeChild(element);

                            window.location =
                            "/search/?search-for=" +
                            encodeURIComponent(stringEditor.value);
                        }
                    );
                }

                {
                    let sectionContainerElement = CBUI.createElement(
                        "CBUI_container1"
                    );

                    element.appendChild(sectionContainerElement);

                    let buttonElement = CBUI.createElement("CBUI_button1");

                    sectionContainerElement.appendChild(buttonElement);

                    buttonElement.textContent = "Cancel";

                    buttonElement.addEventListener(
                        "click",
                        function CBSearchMenuItem_cancel() {
                            document.body.removeChild(element);
                        }
                    );
                }

                stringEditor.focus();
            }
        );
    },
    /* activateElement() */

};
/* CBSearchMenuItem */


Colby.afterDOMContentLoaded(
    function CBSearchMenuItem_afterDOMContentLoaded() {
        let elements = document.getElementsByClassName("CBSearchMenuItem");

        for (let index = 0; index < elements.length; index += 1) {
            CBSearchMenuItem.activateElement(
                elements.item(index)
            );
        }
    }
);
