"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBUISpec_Tests */
/* exported CBUISpec_Tests_DescriptionClass1Editor */
/* exported CBUISpec_Tests_DescriptionClass2Editor */
/* exported CBUISpec_Tests_ThumbnailURLClass1Editor */
/* exported CBUISpec_Tests_ThumbnailURLClass2Editor */
/* exported CBUISpec_Tests_ThumbnailURLClass3Editor */
/* globals
    CBModel,
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
                    className: "CBUISpec_Tests_DescriptionClass1",
                    theTitle: "  Hello  ",
                },
                expectedResult: "Hello",
            },
            {
                spec: {
                    className: "CBUISpec_Tests_DescriptionClass1",
                },
                expectedResult: undefined,
            },
            {
                spec: {
                    className: "CBUISpec_Tests_DescriptionClass2",
                    theTitle: "  Hello  ",
                },
                expectedResult: "Hello",
            },
            {
                spec: {
                    className: "CBBackgroundView",
                    title: "  Hello  ",
                },
                expectedResult: "Hello",
            },
            {
                spec: {
                    className: "CBContainerView",
                    title: "  Hello  ",
                },
                expectedResult: "Hello",
            },
            {
                spec: {
                    className: "CBContainerView2",
                    title: "  Hello  ",
                },
                expectedResult: "Hello",
            },
            {
                spec: {
                    className: "CBIconLinkView",
                    text: "  Hello  ",
                },
                expectedResult: "Hello",
            },
            {
                spec: {
                    className: "CBIconLinkView",
                    alternativeText: "  Hello  ",
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
    CBTest_specToThumbnailURL: function () {
        let tests = [
            {
                spec: {
                    className: "CBUISpec_Tests_ThumbnailURLClass1",
                    theThumbnailURL: "  Hello  ",
                },
                expectedResult: "Hello",
            },
            {
                spec: {
                    className: "CBUISpec_Tests_ThumbnailURLClass1",
                },
                expectedResult: undefined,
            },
            {
                spec: {
                    className: "CBUISpec_Tests_ThumbnailURLClass2",
                    theThumbnailURL: "  Hello  ",
                },
                expectedResult: "Hello",
            },
            {
                spec: {
                    className: "CBUISpec_Tests_ThumbnailURLClass3",
                    theThumbnailURL: "  Hello  ",
                },
                expectedResult: "Hello",
            },
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
            {
                spec: {
                    className: "CBContainerView",
                    smallImage: 5,
                },
                expectedResult: "",
            },
            {
                spec: {
                    className: "CBContainerView2",
                    image: 5,
                },
                expectedResult: "",
            },
        ];

        for (let index = 0; index < tests.length; index += 1) {
            let test = tests[index];
            let actualResult = CBUISpec.specToThumbnailURL(test.spec);

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
    /* CBTest_specToThumbnailURL() */
};
/* CBUISpec_Tests */


var CBUISpec_Tests_DescriptionClass1Editor = {
    CBUISpec_toDescription: function (spec) {
        return CBModel.valueToString(
            spec,
            "theTitle"
        ).trim() || undefined;
    }
};

var CBUISpec_Tests_DescriptionClass2Editor = {
    specToDescription: function (spec) {
        return CBModel.valueToString(
            spec,
            "theTitle"
        ).trim() || undefined;
    }
};

var CBUISpec_Tests_ThumbnailURLClass1Editor = {
    CBUISpec_toThumbnailURL: function (spec) {
        return CBModel.valueToString(
            spec,
            "theThumbnailURL"
        ).trim() || undefined;
    }
};

var CBUISpec_Tests_ThumbnailURLClass2Editor = {
    CBUISpec_toThumbnailURI: function (spec) {
        return CBModel.valueToString(
            spec,
            "theThumbnailURL"
        ).trim() || undefined;
    }
};

var CBUISpec_Tests_ThumbnailURLClass3Editor = {
    specToThumbnailURI: function (spec) {
        return CBModel.valueToString(
            spec,
            "theThumbnailURL"
        ).trim() || undefined;
    }
};
