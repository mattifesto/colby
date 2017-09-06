"use strict"; /* jshint strict: global */
/* globals
    CBUIExpander,
    Colby */

var CBAdminPageForLogs = {

    /**
     * @return Element
     */
    create: function () {
        var element = document.createElement("div");
        element.className = "entries";

        Colby.callAjaxFunction("CBAdminPageForLogs", "fetchEntries")
            .then(onFulfilled)
            .catch(Colby.reportAndDisplayError);

        return element;

        function onFulfilled(entries) {
            entries.forEach(function (entry) {
                var message = entry.message;

                if (entry.model && entry.model.exceptionStackTrace) {
                    message += "\n\n" + entry.model.exceptionStackTrace;
                }

                if (entry.model && entry.model.text) {
                    message += "\n\n" + entry.model.text;
                }

                element.appendChild(CBUIExpander.create({
                    message: message,
                    timestamp: entry.timestamp,
                }).element);
            });

            Colby.updateTimes();
        }
    },
};

Colby.afterDOMContentLoaded(function () {
    var main = document.getElementsByTagName("main")[0];
    main.appendChild(CBAdminPageForLogs.create());
});
