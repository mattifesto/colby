"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* global
    CBUI,
    CBUIExpander,
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
                    .catch(Colby.displayError)
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

        var issuesElement = document.createElement("div");
        issuesElement.className = "issues";

        var fetchIssuesButton = CBUI.createButton({
            callback: fetchIssues,
            text: "Fetch Issues",
        });

        buttonsElement.appendChild(fetchIssuesButton.element);

        function fetchIssues() {
            fetchIssuesButton.disable();

            CBTasks2AdminPage.fetchIssues(issuesElement)
                .catch(Colby.displayAndReportError)
                .then(function () { fetchIssuesButton.enable(); });
        }

        fetchIssues();

        element.appendChild(buttonsElement);
        element.appendChild(issuesElement);

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
     * @param object response
     *
     * @return Element
     */
    createStatusContent: function (response) {
        var element = document.createElement("div");

        element.appendChild(create("Available Tasks", response.countOfAvailableTasks));
        element.appendChild(create("Scheduled Tasks", response.countOfScheduledTasks));
        element.appendChild(create("Last Minute", response.countOfTasksCompletedInTheLastMinute));
        element.appendChild(create("Last Hour", response.countOfTasksCompletedInTheLastHour));
        element.appendChild(create("Last 24 Hours", response.countOfTasksCompletedInTheLast24Hours));
        element.appendChild(create("CBTasks2DispatchDelay", Colby.CBTasks2DispatchDelay));

        return element;

        function create(text, value) {
            var textvalue = document.createElement("div");
            textvalue.className = "textvalue";
            var textElement = document.createElement("div");
            textElement.className = "text";
            textElement.textContent = text;
            var valueElement = document.createElement("div");
            valueElement.className = "value";
            valueElement.textContent = value;

            textvalue.appendChild(textElement);
            textvalue.appendChild(valueElement);

            return textvalue;
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
     * @param issuesElement Element
     *
     * @return Promise
     */
    fetchIssues: function (issuesElement) {
        return Colby.callAjaxFunction("CBTasks2AdminPage", "fetchOutputsWithIssues")
            .then(onFulfilled)
            .catch(Colby.displayAndReportError);

        function onFulfilled(outputs) {
            var count = 0;

            issuesElement.textContent = "";

            for (let output of outputs) {
                var message = output.message + "\n\n" + output.taskClassName + "\n" + output.taskID;

                if (output.exception) {
                    message += "\n\n" + output.exception;
                }

                var expander = CBUIExpander.create({
                    links: output.links,
                    message: message,
                    timestamp: output.completed,
                });

                issuesElement.appendChild(expander.element);

                count += 1;

                if (count >= 100) {
                    break;
                }
            }

            Colby.updateTimes();
        }
    },

    /**
     * @return undefined
     */
    startFetchingStatus: function (element) {
        fetchStatus();

        function fetchStatus() {
            Colby.callAjaxFunction("CBTasks2AdminPage", "fetchStatus")
                .then(onFulfilled)
                .catch(Colby.displayError);
        }

        function onFulfilled(response) {
            element.textContent = "";
            element.appendChild(CBTasks2AdminPage.createStatusContent(response));

            if (response.countOfAvailableTasks > 0) {
                Colby.CBTasks2DispatchDelay = 1; // 1 millisecond
            } else {
                Colby.CBTasks2DispatchDelay = 2000; // 2 seconds
            }

            setTimeout(fetchStatus, 1000);
        }
    },

    /**
     * @return undefined
     */
    scheduleATask: function () {
        Colby.fetchAjaxResponse("/api/?class=CBTasks2AdminPage&function=scheduleATask")
            .then(function () { Colby.alert("A task was scheduled."); })
            .catch(Colby.displayError);
    },
};

Colby.afterDOMContentLoaded(function () {
    Colby.CBTasks2DispatchAlways = true;
    var main = document.getElementsByTagName("main")[0];
    main.appendChild(CBTasks2AdminPage.create());
});
