"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBCodeAdmin */
/* global
    CBMessageMarkup,
    CBModel,
    CBUI,
    CBUIBooleanSwitchPart,
    CBUIExpander,
    Colby,

    CBCodeAdmin_searches,
*/

var CBCodeAdmin = {

    /**
     * @return undefined
     */
    init: function () {
        let elements = document.getElementsByClassName("CBCodeAdmin");

        if (elements.length === 0) {
            return;
        }

        let rootElement = elements.item(0);

        rootElement.appendChild(
            init_createOptionsElement()
        );

        let promise = Promise.resolve();

        CBCodeAdmin_searches.forEach(
            function (search, index) {
                let expander = CBUIExpander.create();
                expander.title = search.title;

                let searchMessage = CBModel.valueToString(
                    search,
                    "message"
                );

                expander.message = searchMessage;

                rootElement.appendChild(expander.element);

                promise = promise.then(
                    function () {
                        return Colby.callAjaxFunction(
                            "CBCodeAdmin",
                            "search",
                            {
                                index: index,
                            }
                        ).then(
                            function (results) {
                                if (results.length === 0) {
                                    expander.severity = 5;
                                    return;
                                } else {
                                    expander.severity = search.severity || 3;
                                }

                                results = results.map(
                                    function (line) {
                                        return CBMessageMarkup.stringToMessage(
                                            line
                                        );
                                    }
                                );

                                results = results.join("\n");

                                expander.message = `

                                    ${searchMessage}

                                    --- pre\n${results}
                                    ---

                                `;
                            }
                        ).catch(
                            function (error) {
                                Colby.displayError(error);
                            }
                        );
                    }
                );
            }
        );

        return;


        /* -- closures -- -- -- -- -- */

        /**
         * @return Element
         */
        function init_createOptionsElement() {
            let sectionContainerElement = CBUI.createElement(
                "CBUI_sectionContainer"
            );

            let sectionElement = CBUI.createElement(
                "CBUI_section"
            );

            sectionContainerElement.appendChild(
                sectionElement
            );

            let sectionItemElement = CBUI.createElement(
                "CBUI_sectionItem CBUI_userSelectNone"
            );

            sectionElement.appendChild(
                sectionItemElement
            );

            let textContainerElement = CBUI.createElement(
                "CBUI_container_topAndBottom CBUI_flexGrow"
            );

            sectionItemElement.appendChild(
                textContainerElement
            );

            let textElement = CBUI.createElement();

            textElement.textContent = "Show searches with no results";

            textContainerElement.appendChild(
                textElement
            );

            let switchPart = CBUIBooleanSwitchPart.create();

            sectionItemElement.appendChild(
                switchPart.element
            );

            switchPart.changed = function () {
                if (switchPart.value) {
                    rootElement.classList.add(
                        "CBCodeAdmin_showSearchesWithNoResults"
                    );
                } else {
                    rootElement.classList.remove(
                        "CBCodeAdmin_showSearchesWithNoResults"
                    );
                }
            };

            return sectionContainerElement;
        }
        /* init_createOptionsElement() */
    },
    /* init() */
};
/* CBCodeAdmin */


Colby.afterDOMContentLoaded(
    function () {
        CBCodeAdmin.init();
    }
);
