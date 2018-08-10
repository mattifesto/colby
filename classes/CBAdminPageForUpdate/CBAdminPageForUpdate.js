"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBAdminPageForUpdate */
/* globals
    CBUI,
    CBUIExpander,
    CBUISection,
    CBUISectionItem4,
    CBUIStringsPart,
    Colby,
*/

var CBAdminPageForUpdate = {

    /**
     * @return undefined
     */
    init: function () {
        var main = document.getElementsByTagName("main")[0];
        var outputElement = document.createElement("div");
        outputElement.className = "output";

        main.appendChild(CBUI.createHalfSpace());

        {
            let section = CBUISection.create();

            /* backup, pull, and update */
            {
                let sectionItem = CBUISectionItem4.create();
                sectionItem.callback = function () {
                    if (taskBegin()) {
                        /**
                         * promiseToPullUpdates() is called twice to ensure new submodules
                         * are properly initialized
                         */

                        promiseToBackupDatabase()
                            .then(promiseToPullUpdates)
                            .then(promiseToPullUpdates)
                            .then(promiseToUpdateSite)
                            .catch(Colby.displayAndReportError)
                            .then(taskEnd);
                    }
                };

                let stringsPart = CBUIStringsPart.create();
                stringsPart.string1 = "Backup, Pull, and Update";

                stringsPart.element.classList.add("action");

                sectionItem.appendPart(stringsPart);
                section.appendItem(sectionItem);
            }

            /* pull and update */
            {
                let sectionItem = CBUISectionItem4.create();
                sectionItem.callback = function () {
                    if (taskBegin()) {
                        promiseToPullUpdates()
                            .then(promiseToUpdateSite)
                            .catch(Colby.displayAndReportError)
                            .then(taskEnd);
                    }
                };

                let stringsPart = CBUIStringsPart.create();
                stringsPart.string1 = "Pull and Update";

                stringsPart.element.classList.add("action");

                sectionItem.appendPart(stringsPart);
                section.appendItem(sectionItem);
            }

            main.appendChild(section.element);
            main.appendChild(CBUI.createHalfSpace());
        }

        {
            let section = CBUISection.create();

            /* backup only */
            {
                let sectionItem = CBUISectionItem4.create();
                sectionItem.callback = function () {
                    if (taskBegin()) {
                        promiseToBackupDatabase()
                            .catch(Colby.displayAndReportError)
                            .then(taskEnd);
                    }
                };

                let stringsPart = CBUIStringsPart.create();
                stringsPart.string1 = "Backup Database";

                stringsPart.element.classList.add("action");

                sectionItem.appendPart(stringsPart);
                section.appendItem(sectionItem);
            }

            /* pull only */
            {
                let sectionItem = CBUISectionItem4.create();
                sectionItem.callback = function () {
                    if (taskBegin()) {
                        promiseToPullUpdates()
                            .catch(Colby.displayAndReportError)
                            .then(taskEnd);
                    }
                };

                let stringsPart = CBUIStringsPart.create();
                stringsPart.string1 = "Git Pull";

                stringsPart.element.classList.add("action");

                sectionItem.appendPart(stringsPart);
                section.appendItem(sectionItem);
            }

            /* update only */
            {
                let sectionItem = CBUISectionItem4.create();
                sectionItem.callback = function () {
                    if (taskBegin()) {
                        promiseToUpdateSite()
                            .catch(Colby.displayAndReportError)
                            .then(taskEnd);
                    }
                };

                let stringsPart = CBUIStringsPart.create();
                stringsPart.string1 = "Update Site";

                stringsPart.element.classList.add("action");

                sectionItem.appendPart(stringsPart);
                section.appendItem(sectionItem);
            }

            main.appendChild(section.element);
            main.appendChild(CBUI.createHalfSpace());
        }

        /* output */

        main.appendChild(outputElement);

        /**
         * closure
         *
         * @return bool
         */
        function taskBegin() {
            if (CBAdminPageForUpdate.taskIsRunning) {
                alert("A task is already running.");
                return false;
            }

            outputElement.textContent = "";
            CBAdminPageForUpdate.taskIsRunning = true;

            return true;
        }

        /* closure */
        function taskEnd() {
            CBAdminPageForUpdate.taskIsRunning = false;
        }

        /* closure */
        function promiseToBackupDatabase() {
            let expander = CBUIExpander.create();
            expander.message = "Starting database backup.";
            expander.timestamp = Date.now() / 1000;

            outputElement.appendChild(expander.element);
            Colby.updateTimes();

            return Colby.fetchAjaxResponse("/developer/mysql/ajax/backup-database/")
                .then(onFulfilled);

            function onFulfilled() {
                expander.message = "Database backup completed.";
                expander.timestamp = Date.now() / 1000;
                Colby.updateTimes();
            }
        }

        /* closure */
        function promiseToPullUpdates() {
            let expander = CBUIExpander.create();
            expander.message = "Starting Git pull.";
            expander.timestamp = Date.now() / 1000;

            outputElement.appendChild(expander.element);
            Colby.updateTimes();

            return Colby.fetchAjaxResponse("/api/?class=CBAdminPageForUpdate&function=pullUpdates")
                .then(onFulfilled);

            function onFulfilled() {
                expander.message = "Git pull completed.";
                expander.timestamp = Date.now() / 1000;
                Colby.updateTimes();
            }
        }

        /* closure */
        function promiseToUpdateSite() {
            let expander = CBUIExpander.create();
            expander.message = "Starting website update.";
            expander.timestamp = Date.now() / 1000;

            outputElement.appendChild(expander.element);
            Colby.updateTimes();

            return Colby.fetchAjaxResponse("/api/?class=CBAdminPageForUpdate&function=update")
                .then(onFulfilled);

            function onFulfilled() {
                expander.message = "Website update completed.";
                expander.timestamp = Date.now() / 1000;
                Colby.updateTimes();
            }
        }
    },
};

Colby.afterDOMContentLoaded(CBAdminPageForUpdate.init);
