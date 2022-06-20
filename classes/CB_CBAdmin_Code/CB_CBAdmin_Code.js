"use strict";
/* jshint strict: global */
/* jshint esversion: 8 */
/* global
    CBErrorHandler,
    CBMessageMarkup,
    CBModel,
    CBUI,
    CBUIBooleanSwitchPart,
    CBUIExpander,
    Colby,

    CB_CBAdmin_Code_CBCodeSearch_CBID,
    CB_CBAdmin_Code_searches,
*/


(function () {

    let rootElement;



    Colby.afterDOMContentLoaded(
        afterDOMContentLoaded
    );



    /**
     * @return undefined
     */
    async function afterDOMContentLoaded() {
        let elements = document.getElementsByClassName(
            "CB_CBAdmin_Code"
        );

        if (elements.length === 0) {
            return;
        }

        rootElement = elements.item(0);

        rootElement.appendChild(
            createOptionsElement()
        );

        for (
            let index = 0;
            index < CB_CBAdmin_Code_searches.length;
            index += 1
        ) {
            try
            {
                let codeSearchModel =
                CB_CBAdmin_Code_searches[
                    index
                ];

                if (
                    CB_CBAdmin_Code_CBCodeSearch_CBID !== ""
                ) {
                    let codeSearchModelCBID =
                    CBModel.getCBID(
                        codeSearchModel
                    );

                    /**
                     * @deprecated 2022_06_19
                     *
                     *      The CBCodeSearch_CBID property is deprecated and has
                     *      been replaced by the model CBID.
                     */

                    if (
                        codeSearchModelCBID ===
                        undefined
                    ) {
                        codeSearchModelCBID =
                        CBModel.valueAsCBID(
                            codeSearchModel,
                            'CBCodeSearch_CBID'
                        );
                    }

                    if (
                        CB_CBAdmin_Code_CBCodeSearch_CBID !==
                        codeSearchModelCBID
                    ) {
                        continue;
                    }
                }

                await
                doSearch(
                    codeSearchModel,
                    index
                );
            }

            catch (
                error
            ) {
                CBErrorHandler.displayAndReport(error);

                break;
            }
        }
    }
    /* afterDOMContentLoaded() */



    /**
     * @return Element
     */
    function createOptionsElement() {
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
                    "CB_CBAdmin_Code_showSearchesWithNoResults"
                );
            } else {
                rootElement.classList.remove(
                    "CB_CBAdmin_Code_showSearchesWithNoResults"
                );
            }
        };

        return sectionContainerElement;
    }
    /* createOptionsElement() */



    /**
     * @param object search
     * @param int index
     *
     * @return Promise -> undefined
     */
    async function
    doSearch(
        codeSearchModel,
        index
    ) {
        let expander = CBUIExpander.create();
        expander.title = codeSearchModel.title;

        let searchCBMessage = CBModel.valueToString(
            codeSearchModel,
            "cbmessage"
        );

        expander.message = searchCBMessage;

        rootElement.appendChild(
            expander.element
        );

        let response = await Colby.callAjaxFunction(
            "CB_CBAdmin_Code",
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
            expander.severity = 6;
            searchResults = "";
        } else {
            expander.severity =
            codeSearchModel.severity ||
            3;

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

        let codeSearchModelCBID =
        CBModel.getCBID(
            codeSearchModel
        );

        /**
         * @deprecated 2022_06_19
         *
         *      The CBCodeSearch_CBID property is deprecated and has
         *      been replaced by the model CBID.
         */

        if (
            codeSearchModelCBID ===
            undefined
        ) {
            codeSearchModelCBID =
            CBModel.valueAsCBID(
                codeSearchModel,
                'CBCodeSearch_CBID'
            );
        }

        let searchOnlyForThisCodeCBMessage = "";

        if (
            codeSearchModelCBID !== undefined
        ) {
            const URL =
            "/admin/" +
            "?c=CB_CBAdmin_Code" +
            `&CBCodeSearch_CBID=${codeSearchModelCBID}`;

            searchOnlyForThisCodeCBMessage = `

                Search only for this code: (link (a ${URL}))

            `;
        }

        expander.message = `

            ${searchCBMessage}

            ${searchOnlyForThisCodeCBMessage}

            (Search Command (b))

            --- pre CBUI_whiteSpace_preWrap\n${searchCommand}
            ---

            (Search Results (b))

            --- pre\n${searchResults}
            ---

        `;
    }
    /* doSearch() */

})();
