"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBUIProcessStatus */
/* global
    CBLog,
    CBUIExpander,
    Colby,
*/

var CBUIProcessStatus = {

    /**
     * @return object
     *
     *      {
     *          append(element)
     *
     *              This function will append an element to the status for use
     *              in cases where a non-log entry related status is needed.
     *              It's best to use a CBUIExpander element.
     *
     *          element: Element (readonly)
     *
     *          processID: hex160 (get, set)
     *
     *              Setting the process ID will cause the CBUIProcessStatus to
     *              continually fetch and display log entries that occur for the
     *              process.
     *
     *      }
     */
    create: function (args) {
        var afterSerial, isFetching, shouldFetchNextQuickly, processID, timeoutID;
        var element = document.createElement("div");
        element.className = "CBUIProcessStatus CBDarkTheme";
        var overviewElement = document.createElement("div");
        overviewElement.className = "overview";
        var entriesElement = document.createElement("div");
        entriesElement.className = "entries";

        element.appendChild(overviewElement);
        element.appendChild(entriesElement);

        return {
            append: function (element) {
                entriesElement.appendChild(element);
                element.scrollIntoView();
            },
            clear: function () {
                entriesElement.textContent = "";
            },
            get element() {
                return element;
            },
            set processID(value) {
                afterSerial = undefined;
                processID = value;
                overviewElement.textContent = "";
                entriesElement.textContent = "";

                let expander = CBUIExpander.create({
                    message: `Process ID: (${processID} (code))`,
                });

                entriesElement.appendChild(expander.element);

                CBLog.fetchMostRecentSerial(processID).then(
                    function (mostRecentSerial) {
                        afterSerial = mostRecentSerial;
                    }
                ).then(
                    fetchStatus
                );
            },
            get processID() {
                return processID;
            },
        };

        /* closure */
        function fetchStatus() {
            if (isFetching) {
                shouldFetchNextQuickly = true;
                return;
            }

            if (timeoutID !== undefined) {
                clearTimeout(timeoutID);
                timeoutID = undefined;
            }

            isFetching = true;

            let ajaxargs = {
                afterSerial: afterSerial,
                processID: processID,
            };

            Colby.callAjaxFunction("CBLog", "fetchEntries", ajaxargs)
                .then(onFetchLogEntriesFulfilled)
                .then(fetchTaskStatus)
                .then(onFetchTaskStatusFulfilled)
                .then(reschedule)
                .catch(Colby.displayAndReportError);

            /* closure */
            function onFetchLogEntriesFulfilled(entries) {
                if (entries.length > 0) {
                    afterSerial = entries[entries.length - 1].serial;

                    entries.forEach(function (entry) {
                        let expander = CBUIExpander.create(entry);
                        entriesElement.appendChild(expander.element);
                        expander.element.scrollIntoView();
                    });

                    Colby.updateTimes();
                }
            }

            /* closure */
            function fetchTaskStatus() {
                return Colby.callAjaxFunction("CBTasks2", "fetchStatus", {processID: processID});
            }

            /* closure */
            function onFetchTaskStatusFulfilled(status) {
                var text = status.complete + " / " + (status.complete + status.ready);

                if (status.scheduled > 0) {
                    text += " Scheduled: " + status.scheduled;
                }

                if (status.ready > 0) {
                    text += " Ready: " + status.ready;
                }

                if (status.running > 0) {
                    text += " Running: " + status.running;
                }
                if (status.failed > 0) {
                    text += " Failed: " + status.failed;
                }

                overviewElement.textContent = text;
            }

            /* closure */
            function reschedule() {
                if (timeoutID === undefined) {
                    let delay = 1000;

                    if (shouldFetchNextQuickly) {
                        delay = 0;
                        shouldFetchNextQuickly = undefined;
                    }

                    timeoutID = setTimeout(fetchStatus, delay);
                    isFetching = undefined;
                } else {
                    throw new Error("CBUIProcessStatus.create() closure reschedule() is being called too often.");
                }
            }
        }
    },
};
