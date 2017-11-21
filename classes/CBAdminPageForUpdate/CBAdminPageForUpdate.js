"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* globals
    CBUI,
    CBUIActionLink,
    CBUIOutput,
    Colby */

var CBAdminPageForUpdate = {

    /**
     * @return undefined
     */
    init: function() {
        var section, item;
        var main = document.getElementsByTagName("main")[0];
        var output = CBUIOutput.create();

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
        main.appendChild(output.element);

        /* closure */
        function backuponly() {
            disable();
            output.clear();

            promiseToBackupDatabase()
                .catch(Colby.displayAndReportError)
                .then(enable);
        }

        /* closure */
        function backupAndUpdate() {
            disable();
            output.clear();

            promiseToBackupDatabase()
                .then(promiseToUpdateSite)
                .catch(Colby.displayAndReportError)
                .then(enable);
        }

        /* closure */
        function backupPullAndUpdate() {
            disable();
            output.clear();

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
            output.append("Starting database backup.");

            return Colby.fetchAjaxResponse("/developer/mysql/ajax/backup-database/")
                .then(() => output.append("Database backup completed."));
        }

        /* closure */
        function promiseToPullUpdates() {
            output.append("Starting Git pull.");

            return Colby.fetchAjaxResponse("/api/?class=CBAdminPageForUpdate&function=pullUpdates")
                .then(() => output.append("Git pull completed."));
        }

        /* closure */
        function promiseToUpdateSite() {
            output.append("Starting website update.");

            return Colby.fetchAjaxResponse("/api/?class=CBAdminPageForUpdate&function=update")
                .then(() => output.append("Website update completed."));
        }

        /* closure */
        function update() {
            disable();
            output.clear();

            promiseToUpdateSite()
                .catch(Colby.displayAndReportError)
                .then(enable);
        }
    },
};

Colby.afterDOMContentLoaded(CBAdminPageForUpdate.init);
