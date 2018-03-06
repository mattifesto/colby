"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBGitStatusAdmin */
/* global
    Colby */

var CBGitStatusAdmin = {

    init: function () {
        var mainElement = document.getElementsByTagName("main")[0];

        fetchStatus();

        function fetchStatus() {
            Colby.callAjaxFunction("CBGitStatusAdmin", "fetchStatus")
                .then(onFulfilled)
                .catch(Colby.displayAndReportError);

            function onFulfilled(value) {
                mainElement.textContent = "";

                let element = document.createElement("div");
                element.textContent = value;

                mainElement.appendChild(element);

                window.setTimeout(fetchStatus, 30000);
            }
        }
    },
};

Colby.afterDOMContentLoaded(CBGitStatusAdmin.init);
