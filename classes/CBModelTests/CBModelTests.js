"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBModelTests */
/* exported CBModelTests_classFunctionTestClass */
/* global
    CBMessageMarkup,
    CBModel,
    CBTest,
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
            let badfunction = CBModel.classFunction(model, "badfunction");

            if (badfunction !== undefined) {
                return {
                    succeeded: false,
                    message: "badfunction test failed",
                };
            }
        }

        {
            let undefinedfunction = CBModel.classFunction(model, "undefinedfunction");

            if (undefinedfunction !== undefined) {
                return {
                    succeeded: false,
                    message: "undefinedfunction test failed",
                };
            }
        }

        {
            let goodfunction = CBModel.classFunction(model, "goodfunction");

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
    CBTest_value: function () {
        let tests = [
            [null, "color", undefined], /* bug fix test */
            [{color: "red"}, "color", "red"],
            [{color: null}, "color.shade", undefined], /* bug fix test */
            [{color: {shade: "light"}}, "color.shade", "light"],
            [{number: 42}, "number", 42],
        ];

        for (let i = 0; i < tests.length; i += 1) {
            let test = tests[i];
            let model = test[0];
            let keyPath = test[1];
            let expected = test[2];

            let actual = CBModel.value(model, keyPath);

            if (actual !== expected) {
                return CBTest.resultMismatchFailure(
                    `Test index ${i}`,
                    actual,
                    expected
                );
            }
        }

        return {
            succeeded: true,
        };
    },
};

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
