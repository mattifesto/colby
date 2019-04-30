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
