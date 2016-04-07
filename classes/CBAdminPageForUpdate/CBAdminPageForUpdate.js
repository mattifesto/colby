"use strict";
/* globals CBUI, CBUIActionLink, Colby */

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
            labelText : "Update Site",
        });
        var updateCallback = ColbySiteUpdater.update.bind(undefined, {
            disableCallback : action.disableCallback,
            enableCallback : action.enableCallback,
        });
        action.updateCallbackCallback(updateCallback);
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
     * @return undefined
     */
    DOMContentDidLoad : function () {
        var main = document.getElementsByTagName("main")[0];
        main.appendChild(ColbySiteUpdater.createElement());
    },

    /**
     * @param function args.disableCallback
     * @param function args.enableCallback
     *
     * @return undefined
     */
    update : function (args) {
        args.disableCallback();

        CSULog.append({message:"Requesting Update..."});

        var xhr = new XMLHttpRequest();
        xhr.onerror = Colby.displayXHRError.bind({xhr:xhr});
        xhr.onload = ColbySiteUpdater.updateDidLoad.bind(undefined, {
            enableCallback : args.enableCallback,
            xhr : xhr,
        });
        xhr.open('POST', '/api/?class=CBAdminPageForUpdate&function=updateForAjax', true);
        xhr.send();
    },

    /**
     * @param function args.enableCallback
     * @param XMLHttpRequest args.xhr
     *
     * @return undefined
     */
    updateDidLoad : function (args) {
        var response = Colby.responseFromXMLHttpRequest(args.xhr);

        if (response.wasSuccessful) {
            CSULog.append({message:"Update succeeded"});
        } else {
            CSULog.append({message:"Update failed"});
        }

        CSULog.append(response);

        // args.enableCallback();
        setTimeout(args.enableCallback, 2000);
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
