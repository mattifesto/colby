"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBUIProcessStatus */
/* global
    CBAjax,
    CBConvert,
    CBLog,
    CBUIExpander,
    CBUIPanel,
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
     *          logEntryMinimumSeverity: int
     *
     *              A higher severity is represented by a lower severity rating.
     *              Setting this property to 6 means that log entries with a
     *              severity rating of 7 or greater will not be shown.
     *
     *              default: 6
     *
     *          processID: hex160 (get, set)
     *
     *              Setting the process ID will cause the CBUIProcessStatus to
     *              continually fetch and display log entries that occur for the
     *              process.
     *      }
     */
    create: function () {
        let afterSerial, isFetching, shouldFetchNextQuickly;
        let processID, timeoutID;
        let logEntryMinimumSeverity = 6;

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

            /**
             * @return int
             */
            get logEntryMinimumSeverity() {
                return logEntryMinimumSeverity;
            },

            /**
             * @param int value
             */
            set logEntryMinimumSeverity(value) {
                value = CBConvert.valueAsInt(value);

                if (value !== undefined) {
                    logEntryMinimumSeverity = value;
                }
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


        /* -- closures -- -- -- -- -- */

        /**
         * @return undefined
         */
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

            CBAjax.call(
                "CBLog",
                "fetchEntries",
                ajaxargs
            ).then(
                onFetchLogEntriesFulfilled
            ).then(
                fetchTaskStatus
            ).then(
                onFetchTaskStatusFulfilled
            ).then(
                reschedule
            ).catch(
                function (error) {
                    CBUIPanel.displayAndReportError(error);
                }
            );

            return;


            /* -- closures -- -- -- -- -- */

            /**
             * @return undefined
             */
            function onFetchLogEntriesFulfilled(entries) {
                if (entries.length > 0) {
                    afterSerial = entries[entries.length - 1].serial;

                    entries.forEach(
                        function (entry) {
                            if (entry.severity <= logEntryMinimumSeverity) {
                                let expander = CBUIExpander.create(entry);
                                entriesElement.appendChild(expander.element);
                                expander.element.scrollIntoView();
                            }
                        }
                    );

                    Colby.updateTimes();
                }
            }
            /* onFetchLogEntriesFulfilled() */


            /**
             * @return Promise
             */
            function fetchTaskStatus() {
                return CBAjax.call(
                    "CBTasks2",
                    "fetchStatus",
                    {
                        processID: processID,
                    }
                );
            }
            /* fetchTaskStatus() */


            /**
             * @return undefined
             */
            function onFetchTaskStatusFulfilled(status) {
                var text = (
                    status.complete +
                    " / " +
                    (status.complete + status.ready)
                );

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
            /* onFetchTaskStatusFulfilled() */


            /**
             * @return undefined
             */
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
                    throw Error(
                        "CBUIProcessStatus.create() closure reschedule() " +
                        "is being called too often."
                    );
                }
            }
            /* reschedule() */
        }
        /* fetchStatus() */
    },
    /* create() */
};
