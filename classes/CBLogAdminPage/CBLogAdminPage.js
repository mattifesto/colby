"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBLogAdminPage */
/* globals
    CBUIExpander,
    Colby */

var CBLogAdminPage = {

    /**
     * @return Element
     */
    create: function () {
        var element = document.createElement("div");
        element.className = "entries";

        Colby.callAjaxFunction("CBLog", "fetchEntries", {mostRecentDescending: true})
            .then(onFulfilled)
            .catch(Colby.displayAndReportError);

        return element;

        function onFulfilled(entries) {
            var count = 0;

            for (let entry of entries) {
                var message = entry.message;

                element.appendChild(CBUIExpander.create({
                    message: message,
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
