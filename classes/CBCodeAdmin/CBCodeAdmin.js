"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBCodeAdmin */
/* global
    CBMessageMarkup,
    CBUI,
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

        let element = elements.item(0);
        let rootElement = CBUI.createElement("CBUIRoot");

        element.appendChild(
            rootElement
        );

        let promise = Promise.resolve();

        CBCodeAdmin_searches.forEach(
            function (search, index) {
                let expander = CBUIExpander.create();
                expander.title = search.title;

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
    },
    /* init() */
};
/* CBCodeAdmin */


Colby.afterDOMContentLoaded(
    function () {
        CBCodeAdmin.init();
    }
);
