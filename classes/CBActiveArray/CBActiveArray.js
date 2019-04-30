"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBActiveArray */
/* global
    CBEvent,
    CBException,
*/

var CBActiveArray = {

    /**
     * @return object
     *
     *      {
     *          push(item)
     *          addEventListener(type, callback)
     *      }
     */
    createPod: function () {
        let items = [];

        let anItemWasAddedEvent = CBEvent.create();

        let pod = {
            addEventListener: addEventListener,
            push: push,
        };

        return pod;

        /* -- closures -- -- -- -- -- */

        /**
         * createPod()
         *   addEventListener()
         *
         * @param string type
         * @param function callback
         *
         * @return undefined
         */
        function addEventListener(type, callback) {
            switch (type) {
                case "anItemWasAdded":
                    anItemWasAddedEvent.addListener(callback);
                    break;

                default:
                    throw new Error(
                        "The event type \"" +
                        type +
                        "\" is not a valid CBActiveArray event type."
                    );
            }
        }
        /* addEventListener() */

        /**
         * createPod()
         *   push()
         *
         * @return int
         */
        function push(item) {
            if (item.CBActiveObject === undefined) {
                throw CBException.withError(
                    Error(
                        [
                            "An item pushed onto a CBActiveArray must be a",
                            "CBActiveObject",
                        ].join(" ")
                    ),
                    ``,
                    "acec6bc55ba1b76acaeb030acbfcef55cb969db3"
                );
            }

            if (items.includes(item)) {
                throw CBException.withError(
                    Error(
                        [
                            "An item was pushed onto a CBActiveArray that is",
                            "already in the active array.",
                        ].join(" ")
                    ),
                    ``,
                    "64033117f4fdb3abf482532e3bf90a81827d2961"
                );
            }

            let length = items.push(item);

            anItemWasAddedEvent.dispatch(item);

            return length;
        }
        /* push() */
    },
};
