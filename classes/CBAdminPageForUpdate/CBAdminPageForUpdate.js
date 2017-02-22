"use strict"; /* jshint strict: global */ /* jshint esversion: 6 */
/* globals
    CBUI,
    CBUIActionLink,
    Colby */

var ColbySiteUpdater = {

    /**
     * @return Element
     */
    createElement : function() {
        var section, item, action;
        var element = document.createElement("div");
        element.className = "ColbySiteUpdater";

        element.appendChild(CBUI.createHalfSpace());

        section = CBUI.createSection();

        ColbySiteUpdater.context = {
            disableCallbacks: [],
            enableCallbacks: [],
        };

        /* backup, pull, and update */
        item = CBUI.createSectionItem();
        action = CBUIActionLink.create({
            callback : ColbySiteUpdater.backupPullAndUpdate,
            labelText : "Backup, Pull, and Update Site",
        });
        item.appendChild(action.element);
        section.appendChild(item);

        ColbySiteUpdater.context.disableCallbacks.push(action.disableCallback);
        ColbySiteUpdater.context.enableCallbacks.push(action.enableCallback);

        /* update only */
        item = CBUI.createSectionItem();
        action = CBUIActionLink.create({
            callback : ColbySiteUpdater.update,
            labelText : "Update Site",
        });
        item.appendChild(action.element);
        section.appendChild(item);

        ColbySiteUpdater.context.disableCallbacks.push(action.disableCallback);
        ColbySiteUpdater.context.enableCallbacks.push(action.enableCallback);

        element.appendChild(section);
        element.appendChild(CBUI.createHalfSpace());

        section = CBUI.createSection();

        /* log */
        item = CBUI.createSectionItem();
        item.appendChild(CSULog.create().element);
        section.appendChild(item);

        element.appendChild(section);
        element.appendChild(CBUI.createHalfSpace());

        return element;
    },

    /**
     * @return undefined
     */
    DOMContentDidLoad : function () {
        var main = document.getElementsByTagName("main")[0];
        main.appendChild(ColbySiteUpdater.createElement());
    },

    /**
     * @return undefined
     */
    finish : function () {
        setTimeout(function () {
            ColbySiteUpdater.context.enableCallbacks.forEach(function (callback) {
                callback.call();
            });
        }, 2000);
    },

    /**
     * @return Promise
     */
    promiseToBackupDatabase : function () {
        CSULog.append({message:"Requesting database backup..."});
        return Colby.fetchAjaxResponse("/developer/mysql/ajax/backup-database/");
    },

    /**
     * @return Promise
     */
    promiseToPullUpdates : function() {
        CSULog.append({message:"Pulling updates..."});
        return Colby.fetchAjaxResponse("/api/?class=CBAdminPageForUpdate&function=pullUpdates");
    },

    /**
     * @return Promise
     */
    promiseToUpdateSite : function () {
        CSULog.append({message:"Requesting site update..."});
        return Colby.fetchAjaxResponse("/api/?class=CBAdminPageForUpdate&function=update");
    },

    /**
     * @return undefined
     */
    reportSuccess : function () {
        CSULog.append({message:"Update completed."});
    },

    /**
     * @return undefined
     */
    backupPullAndUpdate : function () {
        ColbySiteUpdater.context.disableCallbacks.forEach(function (callback) {
            callback.call();
        });

        // promiseToUpdateSite is call twice to ensure new submodules are
        // properly initialized

        ColbySiteUpdater.promiseToBackupDatabase()
            .then(CSULog.appendAjaxResponse)
            .then(ColbySiteUpdater.promiseToPullUpdates)
            .then(CSULog.appendAjaxResponse)
            .then(ColbySiteUpdater.promiseToUpdateSite)
            .then(CSULog.appendAjaxResponse)
            .then(ColbySiteUpdater.promiseToUpdateSite)
            .then(ColbySiteUpdater.reportSuccess)
            .catch(CSULog.appendError)
            .then(ColbySiteUpdater.finish);
    },

    /**
     * @return undefined
     */
    update : function () {
        ColbySiteUpdater.context.disableCallbacks.forEach(function (callback) {
            callback.call();
        });

        // promiseToUpdateSite is call twice to ensure new submodules are
        // properly initialized

        ColbySiteUpdater.promiseToUpdateSite()
            .then(CSULog.appendAjaxResponse)
            .then(ColbySiteUpdater.reportSuccess)
            .catch(CSULog.appendError)
            .then(ColbySiteUpdater.finish);
    },
};

document.addEventListener("DOMContentLoaded", ColbySiteUpdater.DOMContentDidLoad);

/**
 * This is a candidate for becoming CBUILog if it works out well.
 */
var CSULog = {

    logs : {},

    /**
     * @param string? message
     * @param string? name
     *
     * @return undefined
     */
    append : function (args) {
        var message = args.message || "CSULog.append(): No message argument provided.";

        var name = args.name || "default";
        var log = CSULog.logs[name];
        var messageElement = document.createElement("div");
        messageElement.className = "CSULogEntry";
        messageElement.textContent = message;

        log.appendChild(messageElement);
    },

    appendAjaxResponse : function (response) {
        var element = document.createElement("div");
        element.className = "CSULogEntry";
        var message = document.createElement("div");
        message.className = "message";
        message.textContent = response.message;

        element.appendChild(message);

        if (response.description !== undefined) {
            var description = document.createElement("div");
            description.className = "description" +
                ((response.descriptionFormat === "preformatted") ? " preformatted" : "");
            description.textContent = response.description;

            element.appendChild(description);
        }

        CSULog.logs["default"].appendChild(element);
    },

    /**
     * @param Error reason
     *
     * @return undefined
     */
    appendError : function (error) {
        var message = error.message || "CSULog.appendError(): The error has no message.";
        CSULog.append({message: "Error: " + message});
    },

    /**
     * @param string args.name
     *
     * @return {
     *  Element element,
     * }
     */
    create : function (args) {
        args = args || {};
        var name = args.name || "default";

        if (CSULog.logs[name] === undefined) {
            var element = document.createElement("div");
            element.className = "CSULog " + name;
            CSULog.logs[name] = element;

            return {
                element : element,
            };
        } else {
            throw "This log has already been created.";
        }
    },

    /**
     * @return undefined
     */
    replace : function (args) {

    },
};
