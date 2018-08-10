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
    init: function() {
        var main = document.getElementsByTagName("main")[0];
        var outputElement = document.createElement("div");
        outputElement.className = "output";

        main.appendChild(CBUI.createHalfSpace());

        {
            let section = CBUISection.create();

            /* backup, pull, and update */
            {
                let sectionItem = CBUISectionItem4.create();
                sectionItem.callback = backupPullAndUpdate;
                let stringsPart = CBUIStringsPart.create();
                stringsPart.string1 = "Backup Database, Pull, and Update Site";

                stringsPart.element.classList.add("action");

                sectionItem.appendPart(stringsPart);
                section.appendItem(sectionItem);
            }

            /* backup and update */
            {
                let sectionItem = CBUISectionItem4.create();
                sectionItem.callback = backupAndUpdate;
                let stringsPart = CBUIStringsPart.create();
                stringsPart.string1 = "Backup Database and Update Site";

                stringsPart.element.classList.add("action");

                sectionItem.appendPart(stringsPart);
                section.appendItem(sectionItem);
            }

            main.appendChild(section.element);
            main.appendChild(CBUI.createHalfSpace());
        }

        {
            let section = CBUISection.create();

            /* update only */
            {
                let sectionItem = CBUISectionItem4.create();
                sectionItem.callback = update;
                let stringsPart = CBUIStringsPart.create();
                stringsPart.string1 = "Update Site";

                stringsPart.element.classList.add("action");

                sectionItem.appendPart(stringsPart);
                section.appendItem(sectionItem);
            }

            /* backup database only */
            {
                let sectionItem = CBUISectionItem4.create();
                sectionItem.callback = backuponly;
                let stringsPart = CBUIStringsPart.create();
                stringsPart.string1 = "Backup Database";

                stringsPart.element.classList.add("action");

                sectionItem.appendPart(stringsPart);
                section.appendItem(sectionItem);
            }

            main.appendChild(section.element);
            main.appendChild(CBUI.createHalfSpace());
        }

        /* output */

        main.appendChild(outputElement);

        /* closure */
        function backuponly() {
            if (CBAdminPageForUpdate.isDisabled) {
                alert("A task is already running.");
                return;
            }

            disable();
            outputElement.textContent = undefined;

            promiseToBackupDatabase()
                .catch(Colby.displayAndReportError)
                .then(enable);
        }

        /* closure */
        function backupAndUpdate() {
            if (CBAdminPageForUpdate.isDisabled) {
                alert("A task is already running.");
                return;
            }

            disable();
            outputElement.textContent = undefined;

            promiseToBackupDatabase()
                .then(promiseToUpdateSite)
                .catch(Colby.displayAndReportError)
                .then(enable);
        }

        /* closure */
        function backupPullAndUpdate() {
            if (CBAdminPageForUpdate.isDisabled) {
                alert("A task is already running.");
                return;
            }

            disable();
            outputElement.textContent = undefined;

            /**
             * promiseToPullUpdates() is called twice to ensure new submodules
             * are properly initialized
             */

            promiseToBackupDatabase()
                .then(promiseToPullUpdates)
                .then(promiseToPullUpdates)
                .then(promiseToUpdateSite)
                .catch(Colby.displayAndReportError)
                .then(enable);
        }

        /* closure */
        function disable() {
            CBAdminPageForUpdate.isDisabled = true;
        }

        /* closure */
        function enable() {
            CBAdminPageForUpdate.isDisabled = false;
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

        /* closure */
        function update() {
            if (CBAdminPageForUpdate.isDisabled) {
                alert("A task is already running.");
                return;
            }

            disable();
            outputElement.textContent = undefined;

            promiseToUpdateSite()
                .catch(Colby.displayAndReportError)
                .then(enable);
        }
    },
};

Colby.afterDOMContentLoaded(CBAdminPageForUpdate.init);
