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
     *          addEventListener(type, callback)
     *          find(callback)
     *          item(index)
     *          push(item)
     *          slice(begin, end)
     *      }
     */
    createPod: function () {
        let items = [];

        let anItemWasAddedEvent = CBEvent.create();
        let somethingChanged = CBEvent.create();

        let pod = {
            addEventListener: addEventListener,
            find: find,
            item: item,
            push: push,
            slice: items.slice.bind(items),

            get length() {
                return items.length;
            }
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

                case "somethingChanged":
                    somethingChanged.addListener(callback);
                    break;

                default:
                    throw CBException.withError(
                        Error(
                            "The event type \"" +
                            type +
                            "\" is not a valid CBActiveArray event type."
                        ),
                        ``,
                        "baf266926a2fbfb4c1341293f366f8400f048be4"
                    );
            }
        }
        /* addEventListener() */


        /**
         * createPod()
         *   find()
         *
         * @param function callback
         *
         *      This callback can will be passed two parameters;
         *
         *      element:
         *      The current element being processed in the array.
         *
         *      index:
         *      The index of the current element being processed in the array.
         *
         *      Unlike the Array find function this function will not pass the
         *      array itself as the third parameter to the callback because the
         *      array is private.
         *
         * @return mixed
         */
        function find(callback) {
            return items.find(
                function (element, index) {
                    return callback(element, index);
                }
            );
        }
        /* find() */


        /**
         * createPod()
         *   item()
         *
         * @param int index
         *
         * @return mixed
         */
        function item(index) {
            return items[index];
        }
        /* item() */


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
            somethingChanged.dispatch();

            item.CBActiveObject.addEventListener(
                "theObjectDataHasChanged",
                function () {
                    somethingChanged.dispatch();
                }
            );

            item.CBActiveObject.addEventListener(
                "theObjectHasBeenReplaced",
                function (replacementObject) {
                    let index = items.findIndex(
                        function (currentItem) {
                            return currentItem === item;
                        }
                    );

                    items[index] = replacementObject;
                }
            );

            item.CBActiveObject.addEventListener(
                "theObjectHasBeenDeactivated",
                function () {
                    let index = items.findIndex(
                        function (currentItem) {
                            return currentItem === item;
                        }
                    );

                    items.splice(index, 1);
                }
            );

            return length;
        }
        /* push() */
    },
    /* createPod() */
};
