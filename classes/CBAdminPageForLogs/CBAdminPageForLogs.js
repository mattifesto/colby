"use strict"; /* jshint strict: global */
/* globals Colby */

var CBAdminPageForLogs = {

    /**
     * @return Element
     */
    create : function () {
        var element = document.createElement("div");
        element.textContent = "foo";

        CBAdminPageForLogs.fetchLogs({element:element});

        return element;
    },

    /**
     * @return undefined
     */
    DOMContentDidLoad : function () {
        var main = document.getElementsByTagName("main")[0];
        main.appendChild(CBAdminPageForLogs.create());
    },

    /**
     * @param Object log
     */
    elementForLog : function (log) {
        var element = document.createElement("div");
        element.className = "log";
        element.textContent = log.message;

        return element;
    },

    /**
     * @param Element args.element
     *
     * @return undefined
     */
    fetchLogs : function (args) {
        var xhr = new XMLHttpRequest();
        xhr.onerror = Colby.displayXHRError.bind(undefined, {xhr:xhr});
        xhr.onload = CBAdminPageForLogs.fetchLogsDidLoad.bind(undefined, {element:args.element,xhr:xhr});
        xhr.open("POST", "/api/?class=CBLog&function=fetchLogs");
        xhr.send();
    },

    /**
     * @param Element args.element
     * @param XMLHttpRequest args.xhr
     *
     * @return undefined
     */
    fetchLogsDidLoad : function (args) {
        var response = Colby.responseFromXMLHttpRequest(args.xhr);

        if (response.wasSuccessful) {
            if (response.logs.length > 0) {
                args.element.textContent = "";
                response.logs.forEach(function (log) {
                    var logElement = CBAdminPageForLogs.elementForLog(log);
                    args.element.appendChild(logElement);
                });
            } else {
                args.element.textContent = "No logs";
            }
        } else {
            Colby.displayResponse(response);
        }
    },
};

document.addEventListener("DOMContentLoaded", CBAdminPageForLogs.DOMContentDidLoad);
