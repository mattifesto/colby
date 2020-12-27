"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBEvent */

var CBEvent = {

    /**
     * @return object
     *
     *      {
     *          addListener() -> undefined
     *          dispatch(value) -> undefined
     *          removeListener() -> undefined
     *      }
     */
    create: function () {
        let dispatchIsHappening = false;
        let listeners = [];

        return {
            addListener: addListener,
            dispatch: dispatch,
            removeListener: removeListener,
        };

        /* -- closures -- -- -- -- -- */

        /**
         * closure in create()
         *
         * @param function listener
         *
         * @return undefined
         */
        function addListener(listener) {
            if (typeof listener !== "function") {
                throw new TypeError(
                    "The parameter to CBEvent.addListener() must be a function."
                );
            }

            if (!listeners.includes(listener)) {
                listeners.push(listener);
            }
        }

        /**
         * closure in create()
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

        /**
         * closure in create()
         *
         * @param function listener
         *
         * @return undefined
         */
        function removeListener(listener) {
            if (typeof listener !== "function") {
                throw new TypeError(
                    "The parameter to CBEvent.removeListener() must be a " +
                    "function."
                );
            }

            let index = listeners.indexOf(listener);

            if (index > -1) {
                listeners.splice(index, 1);
            }
        }
    },
};
