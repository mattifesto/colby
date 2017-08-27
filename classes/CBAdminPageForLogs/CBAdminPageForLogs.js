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

        var promise = Colby.fetchAjaxResponse("/api/?class=CBLog&function=fetchLogs")
            .then(onFulfilled)
            .catch(onRejected)
            .then(onFinally, onFinally);

        Colby.retain(promise);

        return element;

        function onFulfilled(response) {
            response.logs.forEach(function (log) {
                var message = log.message;

                if (log.model && log.model.exceptionStackTrace) {
                    message += "\n\n" + log.model.exceptionStackTrace;
                }

                if (log.model && log.model.text) {
                    message += "\n\n" + log.model.text;
                }

                element.appendChild(CBUIExpander.create({
                    message: message,
                    timestamp: log.timestamp,
                }).element);

                Colby.updateTimes();
            });
        }

        function onRejected(error) {
            Colby.report(error);
            Colby.displayError(error);
        }

        function onFinally() {
            Colby.release(promise);
        }
    },
};

document.addEventListener("DOMContentLoaded", function () {
    var main = document.getElementsByTagName("main")[0];
    main.appendChild(CBAdminPageForLogs.create());
});
