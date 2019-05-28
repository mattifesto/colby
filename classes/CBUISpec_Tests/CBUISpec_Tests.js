"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBUISpec_Tests */
/* globals
    CBTest,
    CBUISpec,
*/

var CBUISpec_Tests = {

    /* -- tests -- -- -- -- -- */

    /**
     * @return object
     */
    CBTest_specToDescription: function () {
        let tests = [
            {
                spec: {
                    className: "CBBackgroundView",
                    title: "  Hello  ",
                },
                expectedResult: "Hello",
            },
        ];

        for (let index = 0; index < tests.length; index += 1) {
            let test = tests[index];
            let actualResult = CBUISpec.specToDescription(test.spec);

            if (actualResult !== test.expectedResult) {
                return CBTest.resultMismatchFailure(
                    `test at index ${index}`,
                    actualResult,
                    test.expectedResult
                );
            }
        }
        /* for */

        return {
            succeeded: true,
        };
    },
    /* CBTest_specToDescription() */


    /**
     * @return object
     */
    CBTest_specToThumbnailURI: function () {
        let tests = [
            {
                spec: {
                    className: "CBBackgroundView",
                    imageURL: "dfb825beee558e9b5f11a6c33f735ffc96147011",
                },
                expectedResult: "dfb825beee558e9b5f11a6c33f735ffc96147011",
            },
            {
                spec: {
                    className: "CBBackgroundView",
                    image: 5,
                },
                expectedResult: "",
            },
        ];

        for (let index = 0; index < tests.length; index += 1) {
            let test = tests[index];
            let actualResult = CBUISpec.specToThumbnailURI(test.spec);

            if (actualResult !== test.expectedResult) {
                return CBTest.resultMismatchFailure(
                    `test at index ${index}`,
                    actualResult,
                    test.expectedResult
                );
            }
        }
        /* for */

        return {
            succeeded: true,
        };
    },
    /* CBTest_specToThumbnailURI() */
};
/* CBUISpec_Tests */
