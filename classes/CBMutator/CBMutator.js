"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBMutator */

var CBMutator = {

    /**
     * @return object
     *
     *      {
     *          addChangeListener: function
     *          removeChangeListener: function (future)
     *          value: mixed (get, set)
     *
     *              Setting the value will call all the change listeners even if
     *              the value hasn't actually changed. If the value is an object
     *              and one of its properties is changed, the object should be
     *              set as the value again or else the change listeners will not
     *              be called.
     *      }
     */
    create: function () {
        let changeListeners = [];
        let v;

        let api = {
            addChangeListener: addChangeListener,

            get value() {
                return v;
            },

            set value(value) {
                v = value;
                dispatchChangeListener();
            },
        };

        return api;

        /**
         * closure in create()
         *
         * @param function callback
         *
         * @return undefined
         */
        function addChangeListener(callback) {
            if (typeof callback !== "function") {
                throw new TypeError();
            }

            if (changeListeners.includes(callback)) {
                return;
            }

            changeListeners.push(callback);
        }

        /**
         * closure in create()
         *
         * @return undefined
         */
        function dispatchChangeListener() {
            for (let index = 0; index < changeListeners.length; index += 1) {
                changeListeners[index].call();
            }
        }
    }
};
