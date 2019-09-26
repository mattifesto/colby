"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBLogAdminPage */
/* globals
    CBErrorHandler,
    CBModel,
    CBUI,
    CBUIExpander,
    CBUINavigationView,
    CBUISelector,
    Colby,

    CBLogAdminPage_classNames,
*/

var CBLogAdminPage = {

    /**
     * @return Element
     */
    createElement: function () {
        let interfaceElement;

        {
            let navigationView = CBUINavigationView.create();

            interfaceElement = navigationView.element;
        }

        var args = {
            lowestSeverity: 6,
            mostRecentDescending: true,
        };

        var entriesElement = document.createElement("div");
        entriesElement.className = "entries";

        {
            let containerElement = document.createElement("div");

            containerElement.appendChild(CBUI.createHalfSpace());

            let sectionElement = CBUI.createSection();

            {
                let options = CBLogAdminPage_classNames.map(function (className) {
                    return {
                        title: className,
                        value: className,
                    };
                });

                options.unshift({
                    title: "All"
                });

                let selector = CBUISelector.create();
                selector.options = options;
                selector.title = "Class Name";
                selector.onchange = function () {
                    args.className = selector.value;
                    handleArgsChanged();
                };

                sectionElement.appendChild(selector.element);
            }

            {
                let options = [
                    { title: "0: Emergency", value: 0 },
                    { title: "1: Alert", value: 1 },
                    { title: "2: Critical", value: 2 },
                    { title: "3: Error", value: 3 },
                    { title: "4: Warning", value: 4 },
                    { title: "5: Notice", value: 5 },
                    { title: "6: Informational", value: 6 },
                    { title: "7: Debug", value: 7 },
                ];

                let selector = CBUISelector.create();
                selector.options = options;
                selector.title = "Lowest Severity";
                selector.value = args.lowestSeverity;
                selector.onchange = function () {
                    args.lowestSeverity = selector.value;
                    handleArgsChanged();
                };

                sectionElement.appendChild(selector.element);
            }

            containerElement.appendChild(sectionElement);
            containerElement.appendChild(CBUI.createHalfSpace());

            containerElement.appendChild(entriesElement);

            CBUINavigationView.navigate(
                {
                    element: containerElement,
                    title: "Log",
                }
            );
        }

        handleArgsChanged();

        return interfaceElement;


        /* -- closures -- -- -- -- -- */

        /**
         * @return undefined
         */
        function handleArgsChanged() {
            Colby.callAjaxFunction(
                "CBLog",
                "fetchEntries",
                args
            ).then(
                function (entries) {
                    onFulfilled(entries);
                }
            ).catch(
                function (error) {
                    CBErrorHandler.displayAndReport(error);
                }
            );
        }


        /**
         * @param [object] entries
         *
         * @return undefined
         */
        function onFulfilled(entries) {
            var count = 0;

            entriesElement.textContent = "";

            for (let entry of entries) {
                let informationMessage = "";

                {
                    let modelID = CBModel.valueAsID(entry, "modelID");

                    if (modelID !== undefined) {
                        informationMessage += `

                            --- dt
                            modelID
                            ---
                            --- dd
                            ((${modelID} (code)) (a /admin/?c=CBModelInspector&ID=${modelID}))
                            ---

                        `;
                    }
                }

                {
                    let processID = CBModel.valueAsID(entry, "processID");

                    if (processID !== undefined) {
                        informationMessage += `

                            --- dt
                            processID
                            ---
                            --- dd
                            (${processID} (code))
                            ---

                        `;
                    }
                }

                {
                    let sourceClassName = CBModel.valueToString(entry, "sourceClassName");

                    if (sourceClassName !== "") {
                        informationMessage += `

                            --- dt
                            sourceClassName
                            ---
                            --- dd
                            (${sourceClassName} (code))
                            ---

                        `;
                    }
                }

                {
                    let sourceID = CBModel.valueAsID(entry, "sourceID");

                    if (sourceID !== undefined) {
                        informationMessage += `

                            --- dt
                            sourceID
                            ---
                            --- dd
                            (${sourceID} (code))
                            ---

                        `;
                    }
                }

                if (informationMessage != "") {
                    informationMessage = `

                        --- dl
                        ${informationMessage}
                        ---

                    `;
                }

                entriesElement.appendChild(CBUIExpander.create({
                    message: entry.message + informationMessage,
                    severity: entry.severity,
                    timestamp: entry.timestamp,
                }).element);

                count += 1;

                if (count >= 100) {
                    break;
                }
            }

            Colby.updateTimes();
        }
        /* onFulfilled() */
    },
    /* createElement() */
};
/* CBLogAdminPage */


Colby.afterDOMContentLoaded(
    function () {
        let pageElements = document.getElementsByClassName("CBLogAdminPage");

        if (pageElements.length > 0) {
            let pageElement = pageElements[0];

            pageElement.appendChild(
                CBLogAdminPage.createElement()
            );
        }
    }
);
