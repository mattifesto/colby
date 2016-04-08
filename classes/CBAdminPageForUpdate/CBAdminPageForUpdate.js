"use strict";
/* globals CBUI, CBUIActionLink, Colby */
/* jshint esversion: 6 */
/* jshint strict: global */

var ColbySiteUpdater = {

    /**
     * @return Element
     */
    createElement : function() {
        var section, item;
        var element = document.createElement("div");
        element.className = "ColbySiteUpdater";

        element.appendChild(CBUI.createHalfSpace());

        section = CBUI.createSection();

        /* action */
        item = CBUI.createSectionItem();
        var action = CBUIActionLink.create({
            callback : ColbySiteUpdater.update,
            labelText : "Update Site",
        });
        ColbySiteUpdater.context = {
            disableActionLinkCallback : action.disableCallback,
            enableActionLinkCallback : action.enableCallback,
        };
        item.appendChild(action.element);
        section.appendChild(item);

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
     * This could become a convenience function of CSULog.
     *
     * @param string reason
     *
     * @return undefined
     */
    displayReason : function (reason) {
        CSULog.append({message:"Error: " + reason});
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
        setTimeout(ColbySiteUpdater.context.enableActionLinkCallback, 2000);
    },

    /**
     * @return Promise
     */
    promiseToBackupDatabase : function () {
        CSULog.append({message:"Requesting database backup..."});
        return ColbySiteUpdater.promiseToGetAjaxResponse({
            URL : "/developer/mysql/ajax/backup-database/",
        });
    },

    /**
     * @param string args.URL
     *
     * @return Promise
     */
    promiseToGetAjaxResponse : function (args) {
        return new Promise(function (resolve, reject) {
            var xhr = new XMLHttpRequest();
            xhr.onerror = ColbySiteUpdater.getAjaxResponseDidError.bind(undefined, {xhr,resolve,reject});
            xhr.onload = ColbySiteUpdater.getAjaxResponseDidLoad.bind(undefined, {xhr,resolve,reject});
            xhr.open("POST", args.URL);
            xhr.send();
        });
    },

    /**
     * @param function args.reject
     * @param function args.resolve
     * @param XMLHttpRequest args.xhr
     *
     * @return undefined
     */
    getAjaxResponseDidError : function (args) {
        var response = Colby.responseFromXMLHttpRequest(args.xhr);
        args.reject(response.message);
    },

    /**
     * @param function args.reject
     * @param function args.resolve
     * @param XMLHttpRequest args.xhr
     *
     * @return undefined
     */
    getAjaxResponseDidLoad : function (args) {
        var response = Colby.responseFromXMLHttpRequest(args.xhr);

        if (response.wasSuccessful) {
            args.resolve(args);
        } else {
            args.reject(response.message);
        }
    },

    /**
     * @return Promise
     */
    promiseToUpdateSite : function () {
        CSULog.append({message:"Requesting site update..."});
        return ColbySiteUpdater.promiseToGetAjaxResponse({
            URL : "/api/?class=CBAdminPageForUpdate&function=update",
        });
    },

    /**
     * @return undefined
     */
    reportSuccess : function () {
        CSULog.append({message:"Update succeeded"});
    },

    /**
     * @return undefined
     */
    update : function () {
        ColbySiteUpdater.context.disableActionLinkCallback();

        ColbySiteUpdater.promiseToBackupDatabase()
            .then(ColbySiteUpdater.promiseToUpdateSite)
            .then(ColbySiteUpdater.reportSuccess)
            .catch(ColbySiteUpdater.displayReason)
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
     * @return undefined
     */
    append : function (args) {
        if (args.message === undefined) {
            return;
        }

        var name = args.name || "default";
        var log = CSULog.logs[name];
        var message = document.createElement("p");
        message.textContent = args.message;

        log.appendChild(message);
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
