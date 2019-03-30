"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBEvent */

var CBEvent = {

    /**
     * @return object
     *
     *      {
     *          addListener: function
     *          dispatch: function
     *      }
     */
    create: function () {
        let dispatchIsHappening = false;
        let listeners = [];

        return {
            addListener: addListener,
            dispatch: dispatch,
        };

        /**
         * closure in CBEvent.create()
         *
         * @param function listener
         *
         * @return undefined
         */
        function addListener(listener) {
            if (typeof listener !== "function") {
                throw new TypeError("The parameter to addListener must be a function.");
            }

            if (!listeners.includes(listener)) {
                listeners.push(listener);
            }
        }

        /**
         * closure in CBEvent.create()
         *
         * A call to dispatch() on an event while dispatch is happening for that
         * event will be ignored.
         *
         * @param mixed argument (optional)
         *
         *      If the event has argument information to provide when it is
         *      dispatched it should be provided with this argument.
         *
         * @return undefined
         */
        function dispatch(argument) {
            if (dispatchIsHappening) {
                return;
            }

            dispatchIsHappening = true;

            listeners.forEach(
                function (listener) {
                    listener(argument);
                }
            );

            dispatchIsHappening = false;
        }
    },
};
