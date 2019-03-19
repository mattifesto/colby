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
};
