"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBEvent */

var CBEvent = {

    /**
     * @return object
     *
     *      {
     *          addListener(<function>) -> <undefined>
     *          dispatch() -> <undefined>
     *      }
     */
    create: function () {
        let listeners = [];

        return {
            addListener: addListener,
            dispatch: dispatch,
        };

        /**
         * @param <function> listener
         *
         * @return <undefined>
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
         * @return <undefined>
         */
        function dispatch() {
            listeners.forEach(
                function (listener) {
                    listener();
                }
            );
        }
    },
};
