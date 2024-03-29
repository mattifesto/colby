"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBUISpec_Tests */
/* exported CBUISpec_Tests_DescriptionClass1Editor */
/* exported CBUISpec_Tests_ThumbnailURLClass1Editor */
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
            {
                /* uses installed admin menu */
                spec: {
                    className: "CBMenuView",
                    menuID: "3924c0a0581171f86f0708bfa799a3d8c34bd390",
                    selectedItemName: "help",
                },
                expectedResult: "Administration (help)",
            },
            {
                /* references menu that doesn't exist */
                spec: {
                    className: "CBMenuView",
                    menuID: "0528a695d6708d8545ebe73842b33a8eddf0dbac",
                    selectedItemName: "foo",
                },
                expectedResult: undefined,
            },
            {
                spec: {
                    className: "CBMenuView",
                },
                expectedResult: undefined,
            },
            {
                spec: {
                    className: "CBPageListView2",
                    classNameForKind: "  Hello  ",
                },
                expectedResult: "Hello",
            },
            {
                spec: {
                    className: "CBPageListView2",
                    classNameForKind: "    ",
                },
                expectedResult: undefined,
            },
            {
                spec: {
                    className: "CBPageListView2",
                },
                expectedResult: undefined,
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



var CBUISpec_Tests_ThumbnailURLClass1Editor = {
    CBUISpec_toThumbnailURL: function (spec) {
        return CBModel.valueToString(
            spec,
            "theThumbnailURL"
        ).trim() || undefined;
    }
};
