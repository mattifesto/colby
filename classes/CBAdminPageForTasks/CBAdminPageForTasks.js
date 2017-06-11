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

        if (output.linkURI) {
            var link = document.createElement("a");
            link.textContent = output.linkText ? output.linkText : "link";
            link.href = output.linkURI;

            details.appendChild(link);
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
        element.textContent = "Available Tasks: " + response.countOfAvailableTasks +
                              " Scheduled Tasks: " + response.countOfScheduledTasks +
                              " Completed Last Minute: " + response.countOfTasksCompletedInTheLastMinute +
                              " Completed Last Hour: " + response.countOfTasksCompletedInTheLastHour +
                              " Completed Last 24 Hours: " + response.countOfTasksCompletedInTheLast24Hours;

        return element;
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
                /*
                var element = document.createElement("div");
                var description = document.createElement("div");
                description.textContent = output.message;

                element.appendChild(description);

                if (output.linkURI) {
                    var link = document.createElement("a");
                    link.textContent = output.linkText ? output.linkText : "link";
                    link.href = output.linkURI;

                    element.appendChild(link);
                }
                */
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
    Colby.CBTasks2DispatchDelay = 1000;
    var main = document.getElementsByTagName("main")[0];
    main.appendChild(CBAdminPageForTasks.create());
});
