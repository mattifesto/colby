"use strict"; /* jshint strict: global */
/* globals
    CBUI,
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
                var promise = Colby.fetchAjaxResponse("/api/?class=CBTaskForSample&function=start")
                    .then(onFulfilled, Colby.displayError)
                    .then(onFinally, onFinally);

                Colby.retain(promise);

                function onFulfilled() {

                }

                function onFinally() {
                    Colby.release(promise);
                }

            },
            text: "Start CBTaskForSample",
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
     * @param object output
     *
     * @return Element
     */
    createOutputElement: function (output) {
        var element = document.createElement("div");
        element.className = "output";

        var contentElement = document.createElement("div");

        contentElement.addEventListener("click", function() {
            element.classList.toggle("expanded");
        });

        var message = document.createElement("div");
        message.className = "message";
        message.textContent = output.message;

        var details = document.createElement("div");
        details.className = "details";

        details.appendChild(CBAdminPageForTasks.createKeyElement(output.taskClassName, output.taskID));

        if (Array.isArray(output.links) && output.links.length > 0) {
            var section = document.createElement("div");
            section.className = "section flow";

            output.links.forEach(function (link) {
                var anchor = document.createElement("a");
                anchor.textContent = link.text;
                anchor.href = link.URI;

                section.appendChild(anchor);
            });

            details.appendChild(section);
        }

        if (output.exception) {
            var pre = document.createElement("pre");
            pre.textContent = output.exception;

            details.appendChild(pre);
        }

        contentElement.appendChild(message);
        contentElement.appendChild(details);
        element.appendChild(contentElement);

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

        CBAdminPageForTasks.fetchStatus(element);

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

            var promise = Colby.fetchAjaxResponse("/api/?class=CBAdminPageForTasks&function=fetchIssues")
                .then(onFulfilled, Colby.displayError)
                .then(onFinally, onFinally);

            Colby.retain(promise);

            function onFulfilled(response) {
                /* TODO: enabled button */

                response.issues.forEach(function (output) {
                    var element = CBAdminPageForTasks.createOutputElement(output);
                    issuesElement.appendChild(element);
                });
            }

            function onFinally() {
                Colby.release(promise);
            }
        }
    },

    /**
     * @return undefined
     */
    fetchStatus: function (element) {
        CBAdminPageForTasks.fetchStatusPromise =
             fetchStatus()
            .then(display)
            .then(wake)
            .then(restart)
            .catch(Colby.displayError);

        function fetchStatus() {
            return Colby.fetchAjaxResponse("/api/?class=CBAdminPageForTasks&function=fetchStatus");
        }

        function display(response) {
            element.textContent = "";
            element.appendChild(CBAdminPageForTasks.createStatusContent(response));

            if (response.countOfAvailableTasks > 0) {
                Colby.CBTasks2DispatchDelay = 1; // 1 millisecond
            } else {
                Colby.CBTasks2DispatchDelay = 2000; // 2 seconds
            }

            return response;
        }

        function wake() {
            return Colby.fetchAjaxResponse("/api/?class=CBTasks2&function=wakeScheduledTasks");
        }

        function restart() {
            setTimeout(CBAdminPageForTasks.fetchStatus, 1000, element);
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

document.addEventListener("DOMContentLoaded", function () {
    Colby.CBTasks2DispatchAlways = true;
    var main = document.getElementsByTagName("main")[0];
    main.appendChild(CBAdminPageForTasks.create());
});
