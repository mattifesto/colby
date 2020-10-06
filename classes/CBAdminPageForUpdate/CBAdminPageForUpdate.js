"use strict";
/* jshint strict: global */
/* jshint esversion: 8 */
/* globals
    CBAjax,
    CBMaintenance,
    CBMessageMarkup,
    CBUI,
    CBUIExpander,
    CBUINavigationView,
    CBUIPanel,
    Colby,

    CBAdminPageForUpdate_isDevelopmentWebsite,

    CBAdmin_CreateBranchPane,
*/



(function() {

    let taskIsRunning = false;
    let outputElement;

    Colby.afterDOMContentLoaded(
        afterDOMContentLoaded
    );



    /**
     * @return undefined
     */
    function afterDOMContentLoaded() {
        let mainElement = document.getElementsByTagName("main")[0];

        mainElement.appendChild(
            CBUINavigationView.create().element
        );

        let navigationPaneElement = document.createElement("div");

        CBUINavigationView.navigate(
            {
                element: navigationPaneElement,
                title: "Developer Tools",
            }
        );

        if (CBAdminPageForUpdate_isDevelopmentWebsite) {
            /* backup, pull colby, and update */

            navigationPaneElement.appendChild(
                createPullColbySectionElement()
            );
        } else {
            /* backup, pull website, and update */

            let elements = CBUI.createElementTree(
                "CBUI_sectionContainer",
                "CBUI_section",
                "CBUI_action"
            );

            navigationPaneElement.appendChild(
                elements[0]
            );

            let actionElement = elements[2];
            actionElement.textContent = "Backup, Pull Website, and Update";

            actionElement.addEventListener(
                "click",
                function () {
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
                }
            );
        }
        /* backup, pull website, and update */


        /* individual actions */
        {
            let elements = CBUI.createElementTree(
                "CBUI_sectionContainer",
                "CBUI_section"
            );

            navigationPaneElement.appendChild(
                elements[0]
            );

            let sectionElement = elements[1];

            /* backup only */
            {
                let actionElement = CBUI.createElement(
                    "CBUI_action"
                );

                sectionElement.appendChild(
                    actionElement
                );

                actionElement.textContent = "Backup Database";

                actionElement.addEventListener(
                    "click",
                    function () {
                        task(
                            "Backup Database",
                            function () {
                                return promiseToBackupDatabase();
                            }
                        );
                    }
                );
            }
            /* backup only */


            /* pull colby or pull website */
            if (CBAdminPageForUpdate_isDevelopmentWebsite) {
                let actionElement = CBUI.createElement(
                    "CBUI_action"
                );

                sectionElement.appendChild(
                    actionElement
                );

                actionElement.textContent = "Pull Colby";

                actionElement.addEventListener(
                    "click",
                    function () {
                        task(
                            "Pull Colby",
                            function () {
                                return promiseToPullColby();
                            }
                        );
                    }
                );
            } else {
                let actionElement = CBUI.createElement(
                    "CBUI_action"
                );

                sectionElement.appendChild(
                    actionElement
                );

                actionElement.textContent = "Pull Website";

                actionElement.addEventListener(
                    "click",
                    function () {
                        task(
                            "Pull Website",
                            function () {
                                return promiseToPullWebsite();
                            }
                        );
                    }
                );
            }
            /* pull colby or pull website */


            /* update only */
            {
                let actionElement = CBUI.createElement(
                    "CBUI_action"
                );

                sectionElement.appendChild(
                    actionElement
                );

                actionElement.textContent = "Update Site";

                actionElement.addEventListener(
                    "click",
                    function () {
                        task(
                            "Update Site",
                            function () {
                                return promiseToUpdateSite();
                            }
                        );
                    }
                );
            }
            /* update only */


            sectionElement.appendChild(
                CBAdmin_CreateBranchPane.createElement()
            );

        }
        /* individual actions */


        /* output element */

        outputElement = document.createElement("div");
        outputElement.className = "output";

        navigationPaneElement.appendChild(
            outputElement
        );

        return;
    }
    /* afterDOMContentLoaded() */



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
        if (taskIsRunning) {
            let error = new Error("A task is already running.");

            CBUIPanel.displayAndReportError(error);

            return;
        }

        outputElement.textContent = "";
        taskIsRunning = true;

        Promise.resolve().then(
            function () {
                return CBMaintenance.transaction(title, callback);
            }
        ).finally(
            function () {
                taskIsRunning = false;
            }
        ).catch(
            function (error) {
                CBUIPanel.displayAndReportError(error);
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

        let promise = CBAjax.call(
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

        let promise = CBAjax.call(
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

        let promise = CBAjax.call(
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

        let promise = CBAjax.call(
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

})();



/* CBAdmin_CreateBranchPane */
(function () {

    window.CBAdmin_CreateBranchPane = {
        createElement,
    };



    /**
     * @return Element
     */
    function createElement() {
        let actionElement = CBUI.createElement(
            "CBUI_action"
        );

        actionElement.textContent = "Create Development Branch >";

        actionElement.addEventListener(
            "click",
            function () {
                showPane();
            }
        );

        return actionElement;
    }
    /* createElement() */



    /**
     * @return undefined
     */
    function showPane() {
        let elements = CBUI.createElementTree(
            "CBAdmin_CreateBranchPane",
            "CBUI_container1",
            "CBUI_button1"
        );

        {
            let isWorking = false;
            let buttonElement = elements[2];
            buttonElement.textContent = "Create Development Branch";

            buttonElement.addEventListener(
                "click",
                async function () {
                    if (isWorking) {
                        return;
                    }

                    isWorking = true;

                    try {
                        let response = await CBAjax.call(
                            "CBAdminPageForUpdate",
                            "createDevelopmentBranch"
                        );

                        CBUIPanel.displayCBMessage(
                            response.cbmessage
                        );
                    } catch (error) {
                        CBUIPanel.displayAndReportError(
                            error
                        );
                    } finally {
                        isWorking = false;
                    }
                }
            );
        }

        CBUINavigationView.navigate(
            {
                element: elements[0],
                title: "Create Development Branch",
            }
        );
    }
    /* showPane() */

})();
/* CBAdmin_CreateBranchPane */
