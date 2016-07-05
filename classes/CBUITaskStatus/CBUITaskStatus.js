"use strict"; /* jshint strict: global */
/* globals Colby */

var CBUITaskStatus = {

    /**
     * @return {
     *  Element element
     * }
     */
    create : function (args) {
        var element = document.createElement("div");
        element.className = "CBUITaskStatus";
        var pendingTasks = document.createElement("div");
        pendingTasks.className = pendingTasks;
        pendingTasks.textContent = "Pending Tasks: ";
        var count = document.createElement("span");
        count.className = "count";
        var log = document.createElement("div");
        log.className = "log";

        pendingTasks.appendChild(count);
        element.appendChild(pendingTasks);
        element.appendChild(log);

        CBUITaskStatus.update.call(undefined, {
            logElement : log,
            pendingTaskCountElement : count,
        });

        return {
            element : element,
        };
    },

    /**
     * @param Element args.logElement
     * @param Element args.pendingTaskCountElement
     *
     * @return undefined
     */
    update : function (args) {
        var status = CBUITaskStatus.status;

        if (status === undefined) {
            status = {
                logEntries : [],
                waiting : false,
            };

            CBUITaskStatus.status = status;
        }

        if (status.waiting) {
            return;
        }

        var xhr = new XMLHttpRequest();
        xhr.onerror = CBUITaskStatus.updateDidError.bind(undefined, {status:status,xhr:xhr});
        xhr.onload = CBUITaskStatus.updateDidLoad.bind(undefined, {
            logElement : args.logElement,
            pendingTaskCountElement : args.pendingTaskCountElement,
            status : status,
            xhr : xhr,
        });
        xhr.open("POST", "/api/?class=CBTasks&function=getStatus");
        xhr.send();

        status.waiting = true;
    },

    /**
     * @param object args.status
     * @param XMLHttpRequest args.xhr
     *
     * @return undefined
     */
    updateDidError : function (args) {
        args.status.waiting = false;
        args.status.error = true;
    },

    /**
     * @param Element args.logElement
     * @param Element args.pendingTaskCountElement
     * @param object args.status
     * @param XMLHttpRequest args.xhr
     *
     * @return undefined
     */
    updateDidLoad : function (args) {
        args.status.waiting = false;
        var response = Colby.responseFromXMLHttpRequest(args.xhr);

        if (response.succeeded) {
            args.pendingTaskCountElement.textContent = response.pendingTaskCount;

            var lines = response.entries.map(function (entry) {
                return entry.timestamp + " " + entry.category + ": " + entry.message;
            });

            args.logElement.textContent = lines.join("\n");
            args.logElement.scrollTop = args.logElement.scrollHeight;
            window.setTimeout(CBUITaskStatus.update.bind(undefined, {
                logElement : args.logElement,
                pendingTaskCountElement : args.pendingTaskCountElement,
            }), response.timeout);
        } else {
            Colby.displayResponse(response);
        }
    },
};
