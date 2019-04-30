"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBActiveArrayTests */
/* global
    CBActiveArray,
    CBActiveObject,
    CBTest,
*/

var CBActiveArrayTests = {

    /**
     * @return object|Promise
     */
    CBTest_events: function () {
        let anItemWasAdded_lastItem;
        let anItemWasAdded_count = 0;

        let activeArray = CBActiveArray.createPod();

        activeArray.addEventListener(
            "anItemWasAdded",
            anItemWasAdded
        );

        {
            let sourceID;

            try {
                activeArray.addEventListener(
                    "notARealEventType",
                    function () {}
                );
            } catch (error) {
                sourceID = error.CBException.sourceID;
            }

            let expectedSourceID = "baf266926a2fbfb4c1341293f366f8400f048be4";

            if (sourceID !== expectedSourceID) {
                return CBTest.resultMismatchFailure(
                    "addEventListener for bad event type",
                    sourceID,
                    expectedSourceID
                );
            }
        }

        {
            let item0 = { name: "item0" };
            let item1 = { name: "item1" };

            CBActiveObject.activate(item0);
            CBActiveObject.activate(item1);

            activeArray.push(item0);

            if (anItemWasAdded_count !== 1) {
                return CBTest.resultMismatchFailure(
                    "first item added count check",
                    anItemWasAdded_count,
                    1
                );
            }

            if (anItemWasAdded_lastItem !== item0) {
                return CBTest.resultMismatchFailure(
                    "first item added item check",
                    anItemWasAdded_lastItem,
                    item0
                );
            }

            if (activeArray.length !== 1) {
                return CBTest.resultMismatchFailure(
                    "first item added length check",
                    activeArray.length,
                    1
                );
            }

            activeArray.push(item1);

            if (anItemWasAdded_count !== 2) {
                return CBTest.resultMismatchFailure(
                    "second item added count check",
                    anItemWasAdded_count,
                    2
                );
            }

            if (anItemWasAdded_lastItem !== item1) {
                return CBTest.resultMismatchFailure(
                    "second item added item check",
                    anItemWasAdded_lastItem,
                    item1
                );
            }

            if (activeArray.length !== 2) {
                return CBTest.resultMismatchFailure(
                    "second item added length check",
                    activeArray.length,
                    2
                );
            }

            let itemAtIndex0 = activeArray.item(0);

            if (itemAtIndex0 !== item0) {
                return CBTest.resultMismatchFailure(
                    "first item added item check",
                    itemAtIndex0,
                    item0
                );
            }

            let itemAtIndex1 = activeArray.item(1);

            if (itemAtIndex1 !== item1) {
                return CBTest.resultMismatchFailure(
                    "second item added item check",
                    itemAtIndex1,
                    item1
                );
            }
        }

        return {
            succeeded: true,
        };

        /* -- closures -- -- -- -- -- */

        /**
         * CBTest_events()
         *   anItemWasAdded()
         */
        function anItemWasAdded(item) {
            anItemWasAdded_count += 1;
            anItemWasAdded_lastItem = item;
        }
        /* anItemWasAdded() */
    },
    /* CBTest_events() */

    /**
     * @return object|Promise
     */
    CBTest_general: function () {
        let activeArray = CBActiveArray.createPod();

        {
            let sourceID;

            try {
                activeArray.push(5);
            } catch (error) {
                sourceID = error.CBException.sourceID;
            }

            let expectedSourceID = "acec6bc55ba1b76acaeb030acbfcef55cb969db3";

            if (sourceID !== expectedSourceID) {
                return CBTest.resultMismatchFailure(
                    "push non-active item",
                    sourceID,
                    expectedSourceID
                );
            }
        }

        {
            let sourceID;
            let activeObject = {};

            CBActiveObject.activate(activeObject);

            activeArray.push(activeObject);

            try {
                activeArray.push(activeObject);
            } catch (error) {
                sourceID = error.CBException.sourceID;
            }

            let expectedSourceID = "64033117f4fdb3abf482532e3bf90a81827d2961";

            if (sourceID !== expectedSourceID) {
                return CBTest.resultMismatchFailure(
                    "push item already in array",
                    sourceID,
                    expectedSourceID
                );
            }
        }

        return {
            succeeded: true,
        };
    },
    /* CBTest_general() */
};
