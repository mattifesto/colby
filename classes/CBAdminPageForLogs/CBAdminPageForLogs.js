"use strict"; /* jshint strict: global */
/* globals Colby */

var CBAdminPageForLogs = {

    /**
     * @return Element
     */
    create : function () {
        var element = document.createElement("div");
        element.className = "entries";

        CBAdminPageForLogs.promise =
            Colby.fetchAjaxResponse("/api/?class=CBLog&function=fetchLogs")
            .then(display)
            .catch(Colby.displayError);

        return element;

        function display(response) {
            response.logs.forEach(function (log) {
                var entryElement = document.createElement("div");
                entryElement.className = "entry";
                var timeElement = Colby.unixTimestampToElement(log.timestamp);
                var messageElement = document.createElement("div");
                messageElement.className = "message";
                messageElement.textContent = log.message;

                entryElement.appendChild(timeElement);
                entryElement.appendChild(messageElement);
                element.appendChild(entryElement);
            });
        }
    },
};

document.addEventListener("DOMContentLoaded", function () {
    var main = document.getElementsByTagName("main")[0];
    main.appendChild(CBAdminPageForLogs.create());
});
