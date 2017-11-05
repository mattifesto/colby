"use strict";
/* jshint strict: global */
/* exported CBUIProcessStatus */
/* global
    CBUIOutput,
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
        var output = CBUIOutput.create();

        element.appendChild(overviewElement);
        element.appendChild(output.element);

        return {
            element: element,
            set processID(value) {
                processID = value;
                overviewElement.textContent = undefined;
                output.clear();
                output.append("Process ID: " + processID);

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
                        output.append(entry.message);
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
