"use strict";
/* jshint strict: global */
/* jshint esversion: 8 */
/* exported CBCodeAdmin */
/* global
    CBErrorHandler,
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
    async init() {
        let elements = document.getElementsByClassName("CBCodeAdmin");

        if (elements.length === 0) {
            return;
        }

        let rootElement = elements.item(0);

        rootElement.appendChild(
            init_createOptionsElement()
        );

        for (
            let index = 0;
            index < CBCodeAdmin_searches.length;
            index += 1
        ) {
            let search = CBCodeAdmin_searches[index];

            let expander = CBUIExpander.create();
            expander.title = search.title;

            let searchCBMessage = CBModel.valueToString(
                search,
                "cbmessage"
            );

            expander.message = searchCBMessage;

            rootElement.appendChild(
                expander.element
            );

            try {
                let response = await Colby.callAjaxFunction(
                    "CBCodeAdmin",
                    "search",
                    {
                        index: index,
                    }
                );

                let searchResults = CBModel.valueToArray(
                    response,
                    "results"
                );

                if (searchResults.length === 0) {
                    expander.severity = 5;
                    searchResults = "";
                } else {
                    expander.severity = search.severity || 3;

                    let updatedSearchResults = [];

                    for (
                        let index = 0;
                        index < searchResults.length;
                        index += 1
                    ) {
                        let searchResult = searchResults[index];

                        let searchResultAsCBMessage = (
                            CBMessageMarkup.stringToMessage(
                                searchResult
                            )
                        );

                        if (searchResult.match(/^[^0-9]/)) {
                            if (index > 0) {
                                updatedSearchResults.push("");
                                updatedSearchResults.push("");
                                updatedSearchResults.push("");
                            }

                            updatedSearchResults.push(
                                searchResultAsCBMessage
                            );

                            updatedSearchResults.push("");
                        } else {
                            updatedSearchResults.push(
                                searchResultAsCBMessage
                            );
                        }
                    }

                    searchResults = updatedSearchResults.join("\n");
                }

                let searchCommand = CBModel.valueToString(
                    response,
                    "command"
                );

                expander.message = `

                    ${searchCBMessage}

                    (Search Command (b))

                    --- pre CBUI_whiteSpace_preWrap\n${searchCommand}
                    ---

                    (Search Results (b))

                    --- pre\n${searchResults}
                    ---

                `;
            } catch (error) {
                CBErrorHandler.displayAndReport(error);

                break;
            }
        }
        /* for */

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
