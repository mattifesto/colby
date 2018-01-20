"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* globals
    CBUI,
    CBUIActionLink,
    CBUIExpander,
    Colby */

var CBAdminPageForUpdate = {

    /**
     * @return undefined
     */
    init: function() {
        var section, item;
        var main = document.getElementsByTagName("main")[0];
        var outputElement = document.createElement("div");
        outputElement.className = "output";

        main.appendChild(CBUI.createHalfSpace());

        section = CBUI.createSection();

        /* backup, pull, and update */

        item = CBUI.createSectionItem();
        var backupPullAndUpdateActionLink = CBUIActionLink.create({
            callback: backupPullAndUpdate,
            labelText: "Backup Database, Pull, and Update Site",
        });
        item.appendChild(backupPullAndUpdateActionLink.element);
        section.appendChild(item);

        /* backup and update */

        item = CBUI.createSectionItem();
        var backupAndUpdateActionLink = CBUIActionLink.create({
            callback: backupAndUpdate,
            labelText: "Backup Database and Update Site",
        });
        item.appendChild(backupAndUpdateActionLink.element);
        section.appendChild(item);

        /* update only */

        item = CBUI.createSectionItem();
        var updateActionLink = CBUIActionLink.create({
            callback: update,
            labelText: "Update Site",
        });
        item.appendChild(updateActionLink.element);
        section.appendChild(item);

        main.appendChild(section);

        /* backup database only */

        item = CBUI.createSectionItem();
        var backuponlyActionLink = CBUIActionLink.create({
            callback: backuponly,
            labelText: "Backup Database",
        });
        item.appendChild(backuponlyActionLink.element);
        section.appendChild(item);

        main.appendChild(section);

        /* output */

        main.appendChild(CBUI.createHalfSpace());
        main.appendChild(outputElement);

        /* closure */
        function backuponly() {
            disable();
            outputElement.textContent = undefined;

            promiseToBackupDatabase()
                .catch(Colby.displayAndReportError)
                .then(enable);
        }

        /* closure */
        function backupAndUpdate() {
            disable();
            outputElement.textContent = undefined;

            promiseToBackupDatabase()
                .then(promiseToUpdateSite)
                .catch(Colby.displayAndReportError)
                .then(enable);
        }

        /* closure */
        function backupPullAndUpdate() {
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
            backupAndUpdateActionLink.disable();
            backupPullAndUpdateActionLink.disable();
            updateActionLink.disable();
            backuponlyActionLink.disable();
        }

        /* closure */
        function enable() {
            backupAndUpdateActionLink.enable();
            backupPullAndUpdateActionLink.enable();
            updateActionLink.enable();
            backuponlyActionLink.enable();
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
            disable();
            outputElement.textContent = undefined;

            promiseToUpdateSite()
                .catch(Colby.displayAndReportError)
                .then(enable);
        }
    },
};

Colby.afterDOMContentLoaded(CBAdminPageForUpdate.init);
