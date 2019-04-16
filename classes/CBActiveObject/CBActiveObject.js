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
                "The targetObject parameter to CBActiveObject.activate() is " +
                "already active."
            );
        }

        let currentObject = targetObject;
        targetObject = undefined;

        let theObjectDataHasChangedEvent = CBEvent.create();
        let theObjectHasBeenReplacedEvent = CBEvent.create();
        let theObjectHasBeenDeactivatedEvent = CBEvent.create();

        let pod = {
            addEventListener: addEventListener,
            deactivate: deactivate,
            removeEventListener: removeEventListener,
            replace: replace,
            tellListenersThatTheObjectDataHasChanged: tellListenersThatTheObjectDataHasChanged,
        };

        Object.defineProperty(
            currentObject,
            "CBActiveObject",
            {
                configurable: true,
                enumerable: false,
                value: pod,
                writable: false,
            }
        );

        return;

        /* -- closures -- -- -- -- -- */

        /**
         * closure in activate()
         *
         * @param string type
         * @param function callback
         *
         * @return undefined
         */
        function addEventListener(type, callback) {
            switch (type) {
                case "theObjectDataHasChanged":
                    theObjectDataHasChangedEvent.addListener(callback);
                    break;

                case "theObjectHasBeenReplaced":
                    theObjectHasBeenReplacedEvent.addListener(callback);
                    break;

                case "theObjectHasBeenDeactivated":
                    theObjectHasBeenDeactivatedEvent.addListener(callback);
                    break;

                default:
                    throw new Error(
                        "The event type \"" +
                        type +
                        "\" is not a valid CBActiveObject event type."
                    );
            }
        }

        /**
         * closure in activate()
         *
         * Calling deactivate signals to listeners that the purpose the
         * object was originally created for is no longer relevant. In its
         * own context, deactivaton may imply that the object was deleted or
         * some other context specific concept of removal or ending.
         */
        function deactivate() {
            delete currentObject.CBActiveObject;

            theObjectHasBeenDeactivatedEvent.dispatch();
        }

        /**
         * closure in activate()
         *
         * @param string type
         * @param function callback
         *
         * @return undefined
         */
        function removeEventListener(type, callback) {
            switch (type) {
                case "theObjectDataHasChanged":
                    theObjectDataHasChangedEvent.removeListener(callback);
                    break;

                case "theObjectHasBeenReplaced":
                    theObjectHasBeenReplacedEvent.removeListener(callback);
                    break;

                case "theObjectHasBeenDeactivated":
                    theObjectHasBeenDeactivatedEvent.removeListener(callback);
                    break;

                default:
                    throw new Error(
                        "The event type \"" +
                        type +
                        "\" is not a valid CBActiveObject event type."
                    );
            }
        }

        /**
         * closure in activate()
         *
         * @param object replacementObject
         *
         * @return undefined
         */
        function replace(replacementObject) {
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
                    enumerable: false,
                    value: pod,
                    writable: false,
                }
            );

            /**
             * While the event is dispatched both objects will have the
             * CBActiveObject pod.
             */
            theObjectHasBeenReplacedEvent.dispatch(replacementObject);

            delete currentObject.CBActiveObject;

            currentObject = replacementObject;
        }

        /**
         * closure in activate()
         *
         * You must always call this function after you are finished making
         * changes to the data in the object.
         *
         * @return undefined
         */
        function tellListenersThatTheObjectDataHasChanged() {
            theObjectDataHasChangedEvent.dispatch();
        }
    },
};
