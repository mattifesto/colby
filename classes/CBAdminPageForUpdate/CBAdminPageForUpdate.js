"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBAdminPageForUpdate */
/* globals
    CBMaintenance,
    CBMessageMarkup,
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
                    task(
                        "Backup, Pull, and Update",
                        function () {
                            return promiseToBackupDatabase().then(
                                promiseToPullUpdates
                            ).then(
                                promiseToUpdateSite
                            );
                        }
                    );
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
                    task(
                        "Pull and Update",
                        function () {
                            return promiseToPullUpdates().then(
                                promiseToUpdateSite
                            );
                        }
                    );
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
                    task(
                        "Backup Database",
                        function () {
                            return promiseToBackupDatabase();
                        }
                    );
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
                    task(
                        "Git Pull",
                        function () {
                            return promiseToPullUpdates();
                        }
                    );
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
                    task(
                        "Update Site",
                        function () {
                            return promiseToUpdateSite();
                        }
                    );
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
         * TODO maybe make task not a closure
         *
         * This function fully handles an attempt to run a task.
         *
         * @param string title
         * @param function callback
         *
         * @return undefined
         */
        function task(title, callback) {
            if (CBAdminPageForUpdate.taskIsRunning) {
                let error = new Error("A task is already running.");

                report(error);

                return;
            }

            outputElement.textContent = "";
            CBAdminPageForUpdate.taskIsRunning = true;

            Promise.resolve().then(
                function () {
                    return CBMaintenance.transaction(title, callback);
                }
            ).finally(
                function () {
                    CBAdminPageForUpdate.taskIsRunning = false;
                }
            ).catch(
                report
            );

            /**
             * @param Error error
             *
             * @return undefined
             */
            function report(error) {
                alert(error.message);
            }
        }

        /* closure */
        function promiseToBackupDatabase() {
            let expander = CBUIExpander.create();
            expander.title = "database backup in progress";
            expander.timestamp = Date.now() / 1000;

            outputElement.appendChild(expander.element);

            return Colby.fetchAjaxResponse("/developer/mysql/ajax/backup-database/")
                .then(onFulfilled);

            function onFulfilled() {
                expander.title = "database backup completed";
                expander.timestamp = Date.now() / 1000;
            }
        }

        /* closure */
        function promiseToPullUpdates() {
            let expander = CBUIExpander.create();
            expander.title = "git pull in progress";
            expander.timestamp = Date.now() / 1000;

            outputElement.appendChild(expander.element);

            return Colby.callAjaxFunction(
                "CBAdminPageForUpdate",
                "pull"
            ).then(
                onFulfilled
            );

            function onFulfilled(response) {
                let message = [
                    "--- pre green",
                    CBMessageMarkup.stringToMessage(response.output),
                    "---",
                ].join("\n");

                expander.message = message;
                expander.timestamp = Date.now() / 1000;

                if (!response.succeeded) {
                    expander.title = "git pull failed";
                    expander.severity = 3;
                } else {
                    expander.title = "git pull completed";
                }
            }
        }

        /* closure */
        function promiseToUpdateSite() {
            let expander = CBUIExpander.create();
            expander.title = "website update in progress";
            expander.timestamp = Date.now() / 1000;

            outputElement.appendChild(expander.element);

            return Colby.fetchAjaxResponse("/api/?class=CBAdminPageForUpdate&function=update")
                .then(onFulfilled);

            function onFulfilled() {
                expander.title = "website update completed";
                expander.timestamp = Date.now() / 1000;
            }
        }
    },
};

Colby.afterDOMContentLoaded(CBAdminPageForUpdate.init);
