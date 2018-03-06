"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBGitStatusAdmin */
/* global
    CBUIExpander,
    Colby */

var CBGitStatusAdmin = {

    /**
     * @return undefined
     */
    init: function () {
        var mainElement = document.getElementsByTagName("main")[0];

        fetchStatus();

        function fetchStatus() {
            Colby.callAjaxFunction("CBGitStatusAdmin", "fetchStatus")
                .then(onFulfilled)
                .catch(Colby.displayAndReportError);

            function onFulfilled(value) {
                mainElement.textContent = "";

                value.forEach(function (status) {
                    if (status.message) {
                        let expander = CBUIExpander.create();
                        expander.expanded = true;
                        expander.timestamp = Date.now() / 1000;
                        expander.message = status.message;

                        mainElement.appendChild(expander.element);
                    }
                });

                Colby.updateTimes(true);

                window.setTimeout(fetchStatus, 30000);
            }
        }
    },
};

Colby.afterDOMContentLoaded(CBGitStatusAdmin.init);
