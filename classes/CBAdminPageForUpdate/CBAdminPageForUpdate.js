"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBAdminPageForUpdate */
/* globals
    CBErrorHandler,
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

            /* backup, pull website, and update */
            {
                let sectionItem = CBUISectionItem4.create();
                sectionItem.callback = function () {
                    task(
                        "Backup, Pull Website, and Update",
                        function () {
                            return Promise.resolve().then(
                                function () {
                                    return promiseToBackupDatabase();
                                }
                            ).then (
                                function () {
                                    return promiseToPullWebsite();
                                }
                            ).then(
                                function () {
                                    return promiseToUpdateSite();
                                }
                            );
                        }
                    );
                };

                let stringsPart = CBUIStringsPart.create();
                stringsPart.string1 = "Backup, Pull Website, and Update";

                stringsPart.element.classList.add("action");

                sectionItem.appendPart(stringsPart);
                section.appendItem(sectionItem);
            }

            /* pull website and update */
            {
                let sectionItem = CBUISectionItem4.create();
                sectionItem.callback = function () {
                    task(
                        "Pull Website and Update",
                        function () {
                            return promiseToPullWebsite().then(
                                promiseToUpdateSite
                            );
                        }
                    );
                };

                let stringsPart = CBUIStringsPart.create();
                stringsPart.string1 = "Pull Website and Update";

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
                        "Pull Website",
                        function () {
                            return promiseToPullWebsite();
                        }
                    );
                };

                let stringsPart = CBUIStringsPart.create();
                stringsPart.string1 = "Pull Website";

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

        main.appendChild(
            createPullColbySectionElement()
        );

        /* output */

        main.appendChild(outputElement);

        return;



        /* -- closures -- -- -- -- -- */



        /**
         * @return Element
         */
        function createPullColbySectionElement() {
            let sectionContainerElement = CBUI.createElement(
                "CBUI_sectionContainer"
            );

            let sectionElement = CBUI.createElement(
                "CBUI_section"
            );

            sectionContainerElement.appendChild(
                sectionElement
            );

            let actionElement = CBUI.createElement(
                "CBUI_action"
            );

            sectionElement.appendChild(
                actionElement
            );

            actionElement.textContent = "Backup, Pull Colby, and Update";

            actionElement.addEventListener(
                "click",
                function () {
                    task(
                        "Backup and Update Colby",
                        function () {
                            return Promise.resolve().then(
                                function () {
                                    return promiseToBackupDatabase();
                                }
                            ).then(
                                function () {
                                    return promiseToPullColby();
                                }
                            ).then(
                                function () {
                                    return promiseToUpdateSite();
                                }
                            );
                        }
                    );
                }
            );

            return sectionContainerElement;
        }
        /* createPullColbySectionElement() */



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

                CBErrorHandler.displayAndReport(error);

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
                function (error) {
                    CBErrorHandler.displayAndReport(error);
                }
            );
        }
        /* task() */



        /**
         * @return Promise
         */
        function promiseToBackupDatabase() {
            let expander = CBUIExpander.create();
            expander.title = "database backup in progress";
            expander.timestamp = Date.now() / 1000;

            outputElement.appendChild(expander.element);

            let promise = Colby.callAjaxFunction(
                "CBAdminPageForUpdate",
                "backupDatabase"
            ).then(
                function () {
                    expander.title = "database backup completed";
                    expander.timestamp = Date.now() / 1000;
                }
            );

            return promise;
        }
        /* promiseToBackupDatabase() */



        /**
         * @return Promise
         */
        function promiseToPullWebsite() {
            let expander = CBUIExpander.create();
            expander.title = "pull website in progress";
            expander.timestamp = Date.now() / 1000;

            outputElement.appendChild(expander.element);

            let promise = Colby.callAjaxFunction(
                "CBAdminPageForUpdate",
                "pull"
            ).then(
                function (response) {
                    let message = [
                        "--- pre green",
                        CBMessageMarkup.stringToMessage(response.output),
                        "---",
                    ].join("\n");

                    expander.message = message;
                    expander.timestamp = Date.now() / 1000;

                    if (response.succeeded) {
                        expander.title = "pull website completed";
                    } else {
                        expander.title = "pull website failed";
                        expander.severity = 3;
                    }
                }
            );

            return promise;
        }
        /* promiseToPullWebsite() */



        /**
         * @return Promise
         */
        function promiseToPullColby() {
            let expander = CBUIExpander.create();
            expander.title = "pull colby in progress";
            expander.timestamp = Date.now() / 1000;

            outputElement.appendChild(
                expander.element
            );

            let promise = Colby.callAjaxFunction(
                "CBAdminPageForUpdate",
                "pullColby"
            ).then(
                function (response) {
                    let message = [
                        "--- pre green",
                        CBMessageMarkup.stringToMessage(response.output),
                        "---",
                    ].join("\n");

                    expander.message = message;
                    expander.timestamp = Date.now() / 1000;

                    if (response.succeeded) {
                        expander.title = "pull colby completed";
                    } else {
                        expander.title = "pull colby failed";
                        expander.severity = 3;
                    }
                }
            );

            return promise;
        }



        /**
         * @return Promise
         */
        function promiseToUpdateSite() {
            let expander = CBUIExpander.create();
            expander.title = "website update in progress";
            expander.timestamp = Date.now() / 1000;

            outputElement.appendChild(expander.element);

            let promise = Colby.callAjaxFunction(
                "CBAdminPageForUpdate",
                "update"
            ).then(
                function () {
                    expander.title = "website update completed";
                    expander.timestamp = Date.now() / 1000;
                }
            );

            return promise;
        }
        /* promiseToUpdateSite() */

    },
    /* init() */

};

Colby.afterDOMContentLoaded(CBAdminPageForUpdate.init);
