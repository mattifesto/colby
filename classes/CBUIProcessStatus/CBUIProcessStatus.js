"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBUIProcessStatus */
/* global
    CBUIExpander,
    Colby */

var CBUIProcessStatus = {

    /**
     * @return object
     *
     *      {
     *          element: Element
     *          processID: hex160 (getter, setter)
     *      }
     */
    create: function (args) {
        var processID, afterSerial;
        var element = document.createElement("div");
        element.className = "CBUIProcessStatus CBDarkTheme";
        var overviewElement = document.createElement("div");
        overviewElement.className = "overview";
        var entriesElement = document.createElement("div");
        entriesElement.className = "entries";

        element.appendChild(overviewElement);
        element.appendChild(entriesElement);

        return {
            get element() {
                return element;
            },
            set processID(value) {
                processID = value;
                overviewElement.textContent = "";
                entriesElement.textContent = "";

                let expander = CBUIExpander.create({
                    message: "Process ID: " + processID,
                });

                entriesElement.appendChild(expander.element);

                fetchStatus();
            },
            get processID() {
                return processID;
            },
        };

        /* closure */
        function fetchStatus() {
            if (processID === undefined) { // TODO remove, we can do status of "all"
                return;
            }

            Colby.callAjaxFunction("CBLog", "fetchEntries", {afterSerial: afterSerial, processID: processID})
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
                    });
                }
            }

            /* closure */
            function fetchTaskStatus() {
                return Colby.callAjaxFunction("CBTasks2", "fetchStatus", {processID: processID});
            }

            /* closure */
            function onFetchTaskStatusFulfilled(status) {
                var text = status.complete + " / " + status.total;

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
                setTimeout(fetchStatus, 1000);
            }
        }
    },
};
