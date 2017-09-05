"use strict"; /* jshint strict: global */
/* globals
    CBUI,
    CBUIExpander,
    Colby */

var CBAdminPageForTasks = {

    /**
     * @return Element
     */
    create: function () {
        var element = document.createElement("div");
        element.className = "CBAdminPageForTasks";
        var buttonsElement = document.createElement("div");
        buttonsElement.className = "buttons";

        var statusElement = CBAdminPageForTasks.createStatusElement();
        element.appendChild(statusElement);

        buttonsElement.appendChild(CBUI.createButton({
            callback: CBAdminPageForTasks.startVerificationForAllPages,
            text: "Start Verification for All Pages",
        }).element);

        buttonsElement.appendChild(CBUI.createButton({
            callback: CBAdminPageForTasks.startVerificationForNewPages,
            text: "Start Verification for New Pages",
        }).element);

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
            callback: CBAdminPageForTasks.scheduleATask,
            text: "Schedule a Task",
        }).element);

        element.appendChild(buttonsElement);

        var issuesElement = CBAdminPageForTasks.createIssuesElement();
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

        CBAdminPageForTasks.startFetchingStatus(element);

        return element;
    },

    /**
     * @return Element
     */
    createIssuesElement: function () {
        var element = document.createElement("div");
        element.className = "issues";
        var issuesElement = document.createElement("div");
        var button = CBUI.createButton({
            callback: fetchIssues,
            text: "Fetch Issues",
        });

        element.appendChild(button.element);
        element.appendChild(issuesElement);

        fetchIssues();

        return element;

        function fetchIssues() {
            issuesElement.textContent = "";
            /* TODO: disable button */

            var promise = Colby.callAjaxFunction("CBAdminPageForTasks", "fetchIssues")
                .then(onFulfilled)
                .catch(onRejected)
                .then(onFinally, onFinally);

            Colby.retain(promise);

            function onFulfilled(value) {
                value.forEach(function (output) {
                    var message = output.message + "\n\n" + output.className + "\n" + output.taskID;

                    if (output.exception) {
                        message += "\n\n" + output.exception;
                    }

                    var expander = CBUIExpander.create({
                        links: output.links,
                        message: message,
                        timestamp: output.completed,
                    });

                    issuesElement.appendChild(expander.element);
                });

                Colby.updateTimes();
            }

            function onRejected(error) {
                Colby.report(error);
                Colby.displayError(error);
            }

            function onFinally() {
                Colby.release(promise);
            }
        }
    },

    /**
     * @return undefined
     */
    startFetchingStatus: function (element) {
        fetchStatus();

        function fetchStatus() {
            Colby.callAjaxFunction("CBAdminPageForTasks", "fetchStatus")
                .then(onFulfilled)
                .catch(Colby.displayError);
        }

        function onFulfilled(response) {
            element.textContent = "";
            element.appendChild(CBAdminPageForTasks.createStatusContent(response));

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
        CBAdminPageForTasks.restartVerificationForAllPagesPromise =
             Colby.fetchAjaxResponse("/api/?class=CBAdminPageForTasks&function=scheduleATask")
            .catch(Colby.displayError);
    },

    /**
     * @return undefined
     */
    startVerificationForAllPages: function () {
        CBAdminPageForTasks.restartVerificationForAllPagesPromise =
             Colby.fetchAjaxResponse("/api/?class=CBPageVerificationTask&function=startForAllPages")
            .catch(Colby.displayError);
    },

    /**
     * @return undefined
     */
    startVerificationForNewPages: function () {
        CBAdminPageForTasks.restartVerificationForNewPagesPromise =
             Colby.fetchAjaxResponse("/api/?class=CBPageVerificationTask&function=startForNewPages")
            .catch(Colby.displayError);
    },
};

Colby.afterDOMContentLoaded(function () {
    Colby.CBTasks2DispatchAlways = true;
    var main = document.getElementsByTagName("main")[0];
    main.appendChild(CBAdminPageForTasks.create());
});
