"use strict"; /* jshint strict: global */
/* globals Colby */

var CBAdminPageForTasks = {

    /**
     * @return Element
     */
    create: function () {
        var element = document.createElement("div");
        element.className = "CBAdminPageForTasks";

        var allButtonElement = document.createElement("button");
        allButtonElement.textContent = "Start Verification for all Pages";
        allButtonElement.addEventListener("click", CBAdminPageForTasks.startVerificationForAllPages);
        element.appendChild(allButtonElement);

        var newButtonElement = document.createElement("button");
        newButtonElement.textContent = "Start Verification for new Pages";
        newButtonElement.addEventListener("click", CBAdminPageForTasks.startVerificationForNewPages);
        element.appendChild(newButtonElement);

        var scheduleButton = document.createElement("button");
        scheduleButton.textContent = "Schedule a Task";
        scheduleButton.addEventListener("click", CBAdminPageForTasks.scheduleATask);
        element.appendChild(scheduleButton);

        var statusElement = CBAdminPageForTasks.createStatusElement();
        element.appendChild(statusElement);

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
        var message = document.createElement("div");
        message.className = "message";
        message.textContent = output.message;

        var control = document.createElement("div");
        var checkbox = document.createElement("input");
        checkbox.type = "checkbox";
        var checkboxText = document.createElement("span");
        checkboxText.textContent = "show details";
        control.appendChild(checkbox);
        control.appendChild(checkboxText);

        var details = document.createElement("div");
        details.className = "details";

        checkbox.addEventListener("click", function () {
            if (checkbox.checked) {
                details.classList.add("show");
            } else {
                details.classList.remove("show");
            }
        });

        details.appendChild(CBAdminPageForTasks.createKeyElement(output.taskClassName, output.taskID));

        var links = output.links || [];
        links.forEach(function (link) {
            var anchor = document.createElement("a");
            anchor.textContent = link.text;
            anchor.href = link.URI;

            details.appendChild(anchor);
        });

        if (output.exception) {
            var pre = document.createElement("pre");
            pre.textContent = output.exception;

            details.appendChild(pre);
        }

        element.appendChild(message);
        element.appendChild(control);
        element.appendChild(details);

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

        var issuesContainer = document.createElement("div");

        var button = document.createElement("button");
        button.textContent = "Fetch Issues";
        button.addEventListener("click", CBAdminPageForTasks.fetchIssues.bind(undefined, issuesContainer, button));

        element.appendChild(button);
        element.appendChild(issuesContainer);

        CBAdminPageForTasks.fetchIssues(issuesContainer, button);

        return element;
    },

    /**
     * @return null
     */
    fetchIssues: function (container, button) {
        container.textContent = "";
        button.disabled = true;

        CBAdminPageForTasks.fetchStatusPromise =
             fetchIssues()
            .then(display)
            .catch(Colby.displayError);

        function fetchIssues() {
            return Colby.fetchAjaxResponse("/api/?class=CBAdminPageForTasks&function=fetchIssues");
        }

        function display(response) {
            button.disabled = false;

            response.issues.forEach(function (output) {
                var element = CBAdminPageForTasks.createOutputElement(output);
                container.appendChild(element);
            });
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
