"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBActiveObjectTests */
/* global
    CBActiveObject,
    CBTest,
*/

var CBActiveObjectTests = {

    /**
     * @return object | Promise
     */
    CBTest_wasChanged: function () {
        let expectedChangeCount;
        let expectedName;
        let returnValue;
        let testIndex;

        let currentChangeCount = 0;
        let activeObject = {
            name: "Sam",
        };

        CBActiveObject.activate(activeObject);

        activeObject.CBActiveObject.addEventListener(
            "wasChanged",
            handleWasChanged
        );

        testIndex = 1;
        expectedChangeCount = 1;
        expectedName = "Bob";
        activeObject.name = expectedName;

        activeObject.CBActiveObject.wasChanged();

        if (returnValue) {
            return returnValue;
        }

        testIndex = 2;
        expectedChangeCount = 2;
        expectedName = "Fred";
        activeObject.name = expectedName;

        activeObject.CBActiveObject.wasChanged();

        if (returnValue) {
            return returnValue;
        }

        if (currentChangeCount !== expectedChangeCount) {
            return CBTest.resultMismatchFailure(
                `Final Change Count`,
                currentChangeCount,
                expectedChangeCount
            );
        }

        return {
            succeeded: true,
        };

        /**
         * @return undefined
         */
        function handleWasChanged() {
            currentChangeCount += 1;

            if (currentChangeCount !== expectedChangeCount) {
                returnValue = CBTest.resultMismatchFailure(
                    `Test ${testIndex}: Change Count`,
                    currentChangeCount,
                    expectedChangeCount
                );

                return;
            }

            if (activeObject.name !== expectedName) {
                returnValue = CBTest.resultMismatchFailure(
                    `Test ${testIndex}: Name Check`,
                    activeObject.name,
                    expectedName
                );

                return;
            }
        }
    },

    /**
     * @return object | Promise
     */
    CBTest_wasRemoved: function () {
        let currentChangeCount = 0;
        let currentRemovalCount = 0;
        let activeObject = {};

        CBActiveObject.activate(activeObject);

        activeObject.CBActiveObject.addEventListener(
            "wasChanged",
            handleWasChanged
        );

        activeObject.CBActiveObject.addEventListener(
            "wasRemoved",
            handleWasRemoved
        );

        activeObject.CBActiveObject.wasChanged();
        activeObject.CBActiveObject.wasChanged();
        activeObject.CBActiveObject.remove();

        if (activeObject.CBActiveObject !== undefined) {
            return {
                succeeded: false,
                message: "activeObject.CBActiveObject should be undefined",
            };
        }

        let expectedChangeCount = 2;

        if (currentChangeCount !== expectedChangeCount) {
            return CBTest.resultMismatchFailure(
                `Final Change Count`,
                currentChangeCount,
                expectedChangeCount
            );
        }

        let expectedRemovalCount = 1;

        if (currentRemovalCount !== expectedRemovalCount) {
            return CBTest.resultMismatchFailure(
                `Final Removal Count`,
                currentRemovalCount,
                expectedRemovalCount
            );
        }

        return {
            succeeded: true,
        };

        /**
         * @return undefined
         */
        function handleWasChanged() {
            currentChangeCount += 1;
        }

        function handleWasRemoved() {
            currentRemovalCount += 1;
        }
    },

    /**
     * @return object | Promise
     */
    CBTest_wasReplaced: function () {
        let expectedChangeCount;
        let expectedName;
        let returnValue;
        let testIndex;

        let activeObject0 = {
            name: "Sam",
        };
        let currentActiveObject = activeObject0;
        let currentChangeCount = 0;

        CBActiveObject.activate(activeObject0);

        currentActiveObject.CBActiveObject.addEventListener(
            "wasChanged",
            handleWasChanged
        );

        currentActiveObject.CBActiveObject.addEventListener(
            "wasReplaced",
            handleWasReplaced
        );

        testIndex = 1;
        expectedChangeCount = 1;
        expectedName = "Bob";
        let activeObject1 = {
            name: expectedName,
        };

        currentActiveObject.CBActiveObject.replace(activeObject1);

        if (returnValue) {
            return returnValue;
        }

        if (activeObject0.CBActiveObject !== undefined) {
            return {
                succeeded: false,
                message: "activeObject0.CBActiveObject should be undefined",
            };
        }

        testIndex = 2;
        expectedChangeCount = 2;
        expectedName = "Fred";
        let activeObject2 = {
            name: expectedName,
        };

        currentActiveObject.CBActiveObject.replace(activeObject2);

        if (returnValue) {
            return returnValue;
        }

        if (activeObject1.CBActiveObject !== undefined) {
            return {
                succeeded: false,
                message: "activeObject1.CBActiveObject should be undefined",
            };
        }

        if (currentChangeCount !== expectedChangeCount) {
            return CBTest.resultMismatchFailure(
                `Final Change Count`,
                currentChangeCount,
                expectedChangeCount
            );
        }

        return {
            succeeded: true,
        };

        /**
         * @return undefined
         */
        function handleWasChanged() {
            currentChangeCount += 1;

            if (currentChangeCount !== expectedChangeCount) {
                returnValue = CBTest.resultMismatchFailure(
                    `Test ${testIndex}: Change Count`,
                    currentChangeCount,
                    expectedChangeCount
                );

                return;
            }

            if (currentActiveObject.name !== expectedName) {
                returnValue = CBTest.resultMismatchFailure(
                    `Test ${testIndex}: Name Check`,
                    currentActiveObject.name,
                    expectedName
                );

                return;
            }
        }

        /**
         * @param object replacementActiveObject
         *
         * @return undefined
         */
        function handleWasReplaced(replacementActiveObject) {
            currentActiveObject = replacementActiveObject;
        }
    },
};
