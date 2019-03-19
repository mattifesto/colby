"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBActiveObject */
/* global
    CBEvent,
*/

var CBActiveObject = {

    /**
     * @param object targetObject
     *
     * @return undefined
     */
    activate: function (targetObject) {
        if (targetObject.CBActiveObject !== undefined) {
            throw new Error(
                "The targetObject parameter value to " +
                "CBActiveObject.activate() is already active."
            );
        }

        let currentObject = targetObject;
        targetObject = undefined;

        let changedEvent = CBEvent.create();
        let replacedEvent = CBEvent.create();
        let removedEvent = CBEvent.create();

        let api = {
            addEventListener: function (type, callback) {
                switch (type) {
                    case "wasChanged":
                        changedEvent.addListener(callback);
                        break;

                    case "wasReplaced":
                        replacedEvent.addListener(callback);
                        break;

                    case "wasRemoved":
                        removedEvent.addListener(callback);
                        break;

                    default:
                        throw new Error("unrecognized event type");
                }
            },

            wasChanged: function () {
                changedEvent.dispatch();
            },

            replace: function (replacementObject) {
                if (replacementObject.CBActiveObject !== undefined) {
                    throw new Error(
                        "An active object can't be replaced with another " +
                        "active object."
                    );
                }

                Object.defineProperty(
                    replacementObject,
                    "CBActiveObject",
                    {
                        configurable: true,
                        value: api,
                    }
                );

                delete currentObject.CBActiveObject;

                currentObject = replacementObject;

                replacedEvent.dispatch(replacementObject);
                changedEvent.dispatch();
            },

            remove: function () {
                delete currentObject.CBActiveObject;

                removedEvent.dispatch();
            },
        };

        Object.defineProperty(
            currentObject,
            "CBActiveObject",
            {
                configurable: true,
                value: api,
            }
        );

        return;
    },
};
