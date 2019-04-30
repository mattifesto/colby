"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBActiveArrayTests */
/* global
    CBActiveArray,
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

        return {
            succeeded: true,
        };
    },
    /* CBTest_general() */
};
