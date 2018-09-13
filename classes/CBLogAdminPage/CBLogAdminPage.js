"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBLogAdminPage */
/* globals
    CBModel,
    CBUI,
    CBUIExpander,
    CBLogAdminPage_classNames,
    CBUINavigationView,
    CBUISelector,
    Colby,
*/

var CBLogAdminPage = {

    /**
     * @return Element
     */
    create: function () {
        let navigator = CBUINavigationView.create();
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

            navigator.navigateToItemCallback({
                element: containerElement,
                title: "Log",
            });
        }

        handleArgsChanged();

        return navigator.element;

        function handleArgsChanged() {
            Colby.callAjaxFunction("CBLog", "fetchEntries", args)
                .then(onFulfilled)
                .catch(Colby.displayAndReportError);
        }

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
    },
};

Colby.afterDOMContentLoaded(function () {
    var main = document.getElementsByTagName("main")[0];
    main.appendChild(CBLogAdminPage.create());
});
