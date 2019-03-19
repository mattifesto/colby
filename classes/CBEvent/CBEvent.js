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
         * @param mixed argument (optional)
         *
         *      If the event has argument information to provide when it is
         *      dispatched it should be provided with this argument.
         *
         * @return undefined
         */
        function dispatch(argument) {
            listeners.forEach(
                function (listener) {
                    listener(argument);
                }
            );
        }
    },
};
