"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBTasks2AdminPage */
/* global
    CBUI,
    Colby */

var CBTasks2AdminPage = {

    /**
     * @return Element
     */
    create: function () {
        var element = document.createElement("div");
        element.className = "CBTasks2AdminPage";
        var buttonsElement = document.createElement("div");
        buttonsElement.className = "buttons";

        var statusElement = CBTasks2AdminPage.createStatusElement();
        element.appendChild(statusElement);

        buttonsElement.appendChild(CBUI.createButton({
            callback: function() {
                var promise = Colby.callAjaxFunction("CBTestTask", "start")
                    .then(onFulfilled)
                    .catch(Colby.displayAndReportError)
                    .then(onFinally, onFinally);

                Colby.retain(promise);

                function onFulfilled() {
                    Colby.alert("CBTestTask started");
                }

                function onFinally() {
                    Colby.release(promise);
                }

            },
            text: "Start CBTestTask",
        }).element);

        buttonsElement.appendChild(CBUI.createButton({
            callback: CBTasks2AdminPage.scheduleATask,
            text: "Schedule a Task",
        }).element);

        element.appendChild(buttonsElement);

        return element;
    },

    /**
     * @return Element
     */
    createKeyElement: function (className, ID) {
        var element = document.createElement("div");
        element.className = "key";

        var classNameElement = document.createElement("div");
        classNameElement.className = "className";
        classNameElement.textContent = className;

        var IDElement = document.createElement("div");
        IDElement.className = "ID";
        IDElement.textContent = ID;

        element.appendChild(classNameElement);
        element.appendChild(IDElement);

        return element;
    },

    /**
     * @param object status
     *
     * @return Element
     */
    createStatusContent: function (status) {
        var element = document.createElement("div");

        element.appendChild(create("Scheduled Tasks", status.scheduled));
        element.appendChild(create("Ready Tasks", status.ready));
        element.appendChild(create("Running Tasks", status.running));
        element.appendChild(create("Complete Tasks", status.complete));
        element.appendChild(create("Failed Tasks", status.failed));
        element.appendChild(create("CBTasks2Delay", Colby.CBTasks2Delay));

        return element;

        /* closure */
        function create(text, value) {
            var textAndValueElement = document.createElement("div");
            textAndValueElement.className = "textvalue";
            var textElement = document.createElement("div");
            textElement.className = "text";
            textElement.textContent = text;
            var valueElement = document.createElement("div");
            valueElement.className = "value";
            valueElement.textContent = value;

            textAndValueElement.appendChild(textElement);
            textAndValueElement.appendChild(valueElement);

            return textAndValueElement;
        }
    },

    /**
     * @return Element
     */
    createStatusElement: function () {
        var element = document.createElement("div");
        element.className = "status";

        CBTasks2AdminPage.startFetchingStatus(element);

        return element;
    },

    /**
     * @return undefined
     */
    startFetchingStatus: function (element) {
        fetchStatus();

        function fetchStatus() {
            Colby.callAjaxFunction("CBTasks2", "fetchStatus")
                .then(onFulfilled)
                .catch(Colby.displayAndReportError);
        }

        function onFulfilled(value) {
            element.textContent = "";
            element.appendChild(CBTasks2AdminPage.createStatusContent(value));

            if (value.ready > 0) {
                Colby.CBTasks2Delay = 1; // 1 millisecond
            } else {
                Colby.CBTasks2Delay = 2000; // 2 seconds
            }

            setTimeout(fetchStatus, 1000);
        }
    },

    /**
     * @return undefined
     */
    scheduleATask: function () {
        Colby.callAjaxFunction("CBTasks2AdminPage", "scheduleATask")
            .then(() => Colby.alert("A task was scheduled."))
            .catch(Colby.displayAndReportError);
    },
};

Colby.afterDOMContentLoaded(function () {
    Colby.CBTasks2RunAlways = true;
    var main = document.getElementsByTagName("main")[0];
    main.appendChild(CBTasks2AdminPage.create());
});
