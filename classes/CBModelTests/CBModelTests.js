"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBModelTests */
/* exported CBModelTests_classFunctionTestClass */
/* global
    CBMessageMarkup,
    CBModel,
    CBTest,

    CBModelTests_CBTest_value_testCases,
*/

var CBModelTests = {

    /**
     * @return object
     */
    CBTest_classFunction: function () {
        let model = {
            className: "CBModelTests_classFunctionTestClass",
        };

        {
            let badfunction = CBModel.getClassFunction(
                model,
                "badfunction"
            );

            if (badfunction !== undefined) {
                return {
                    succeeded: false,
                    message: "badfunction test failed",
                };
            }
        }

        {
            let undefinedfunction = CBModel.getClassFunction(
                model,
                "undefinedfunction"
            );

            if (undefinedfunction !== undefined) {
                return {
                    succeeded: false,
                    message: "undefinedfunction test failed",
                };
            }
        }

        {
            let goodfunction = CBModel.getClassFunction(
                model,
                "goodfunction"
            );

            if (typeof goodfunction !== "function") {
                return {
                    succeeded: false,
                    message: "goodfunction test failed",
                };
            }

            return goodfunction();
        }
    },

    /**
     * @return object
     */
    CBTest_equals: function () {
        let equalValues = [
            [false, false],
            [0, 0],
            ["", ""],
            [undefined, undefined],
            [null, null],
            [{}, {}],
            [[], []],
            [42.42, 42.42],
            [
                {
                    foo: "bar",
                    num: 1,
                    so: true,
                    non: null,
                    obj: {
                        name: "bob",
                        quan: 5,
                    },
                    arr: [5, 4, "3"],
                },
                {
                    num: 1,
                    non: null,
                    arr: [5, 4, "3"],
                    foo: "bar",
                    so: true,
                    obj: {
                        quan: 5,
                        name: "bob",
                    },
                }
            ],
            [
                /* explicitly undefined properties test */
                {
                    a: "b",
                    w: undefined,
                    x: undefined,
                },
                {
                    a: "b",
                    y: undefined,
                }
            ]
        ];

        for (let i = 0; i < equalValues.length; i += 1) {
            let values = equalValues[i];

            if (!CBModel.equals(values[0], values[1])) {
                let valuesAsMessage = CBMessageMarkup.stringToMessage(
                    JSON.stringify(values)
                );
                let message = `

                    CBModel.equal() was expected to consider the following
                    values to be equal.

                    --- pre\n${valuesAsMessage}
                    ---

                `;

                return {
                    message: message,
                };
            }
        }

        let nonequalValues = [
            [false, true],
            [false, []],
            [0, 1],
            [0, null],
            ["", "hi"],
            ["", false],
            [undefined, null],
            [undefined, {a:1}],
            [null, 1],
            [[1,2,3], [1,2,4]],
            [{}, []],
            [{a:1,b:2}, {a:1,b:3}],
            [[], {}],
            [42.42, 42.43],
            [
                {
                    foo: "bar",
                    num: 1,
                    so: true,
                    non: null,
                    obj: {
                        name: "bob",
                        quan: 5, /* different */
                    },
                    arr: [5, 4, "3"],
                },
                {
                    num: 1,
                    non: null,
                    arr: [5, 4, "3"],
                    foo: "bar",
                    so: true,
                    obj: {
                        quan: 6, /* different */
                        name: "bob",
                    },
                }
            ],
        ];

        for (let i = 0; i < nonequalValues.length; i += 1) {
            let values = nonequalValues[i];

            if (CBModel.equals(values[0], values[1])) {
                let valuesAsMessage = CBMessageMarkup.stringToMessage(
                    JSON.stringify(values)
                );
                let message = `

                    CBModel.equal() was expected to consider the following
                    values to be not equal.

                    --- pre\n${valuesAsMessage}
                    ---

                `;

                return {
                    message: message,
                };
            }
        }

        return {
            succeeded: true,
        };
    },



    /**
     * @return object|Promise
     */
    CBTest_value() {
        let testCases = CBModelTests_CBTest_value_testCases;

        for (
            let index = 0;
            index < testCases.length;
            index += 1
        ) {
            let testCase = testCases[index];

            let actualResult = CBModel.value(
                testCase.originalValue,
                testCase.keyPath
            );

            let expectedResult = (
                testCase.expectedResult === null ?
                undefined :
                testCase.expectedResult
            );

            if (actualResult !== expectedResult) {
                return CBTest.resultMismatchFailure(
                    `test index ${index}`,
                    actualResult,
                    expectedResult
                );
            }
        }

        return {
            succeeded: true,
        };
    },
    /* CBTest_value() */

};
/* CBModelTests */



/**
 *
 */
var CBModelTests_classFunctionTestClass = {
    badfunction: "not a function",

    goodfunction: function () {
        return {
            succeeded: true,
        };
    },
};
