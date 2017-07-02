"use strict"; /* jshint strict: global */
/* globals
    CBUIExpandableRow,
    Colby */

var CBAdminPageForLogs = {

    /**
     * @return Element
     */
    create: function () {
        var element = document.createElement("div");
        element.className = "entries";

        CBAdminPageForLogs.promise =
            Colby.fetchAjaxResponse("/api/?class=CBLog&function=fetchLogs")
            .then(display)
            .catch(Colby.displayError);

        return element;

        function display(response) {
            response.logs.forEach(function (log) {

                var title = log.message.substr(0, 100);
                var titleElement = document.createElement("div");
                titleElement.textContent = title;

                var timeElement = Colby.unixTimestampToElement(log.timestamp);

                var row = CBUIExpandableRow.create();
                row.columnsElement.appendChild(timeElement);
                row.columnsElement.appendChild(titleElement);


                if (title.length !== log.message.length) {
                    var messageElement = document.createElement("div");
                    messageElement.textContent = log.message;

                    row.contentElement.appendChild(messageElement);
                }

                if (log.model && log.model.exceptionStackTrace) {
                    var exceptionStackTraceElement = document.createElement("pre");
                    exceptionStackTraceElement.textContent = log.model.exceptionStackTrace;

                    row.contentElement.appendChild(exceptionStackTraceElement);
                }

                element.appendChild(row.element);
            });
        }
    },
};

document.addEventListener("DOMContentLoaded", function () {
    var main = document.getElementsByTagName("main")[0];
    main.appendChild(CBAdminPageForLogs.create());
});
