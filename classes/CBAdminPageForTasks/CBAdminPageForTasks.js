"use strict"; /* jshint strict: global */
/* globals Colby */

var CBAdminPageForTasks = {

    /**
     * @return Element
     */
    create : function () {
        var element = document.createElement("div");

        CBAdminPageForTasks.fetchTasks({element:element});

        return element;
    },

    /**
     * @return undefined
     */
    DOMContentDidLoad : function () {
        var main = document.getElementsByTagName("main")[0];
        main.appendChild(CBAdminPageForTasks.create());
    },

    /**
     * @param Object task
     */
    elementForTask : function (task) {
        var element = document.createElement("div");
        element.className = "task";
        element.textContent = task.className + "::" + task.function;

        return element;
    },

    /**
     * @param Element args.element
     *
     * @return undefined
     */
    fetchTasks : function (args) {
        var xhr = new XMLHttpRequest();
        xhr.onerror = Colby.displayXHRError.bind(undefined, {xhr:xhr});
        xhr.onload = CBAdminPageForTasks.fetchTasksDidLoad.bind(undefined, {element:args.element,xhr:xhr});
        xhr.open("POST", "/api/?class=CBTasks&function=fetchTasks");
        xhr.send();
    },

    /**
     * @param Element args.element
     * @param XMLHttpRequest args.xhr
     *
     * @return undefined
     */
    fetchTasksDidLoad : function (args) {
        var response = Colby.responseFromXMLHttpRequest(args.xhr);

        if (response.wasSuccessful) {
            if (response.tasks.length > 0) {
                args.element.textContent = "";
                response.tasks.forEach(function (task) {
                    var taskElement = CBAdminPageForTasks.elementForTask(task);
                    args.element.appendChild(taskElement);
                });
            } else {
                args.element.textContent = "No tasks";
            }
        } else {
            Colby.displayResponse(response);
        }
    },
};

document.addEventListener("DOMContentLoaded", CBAdminPageForTasks.DOMContentDidLoad);
