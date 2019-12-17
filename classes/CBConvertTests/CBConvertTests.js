"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBConvertTests */
/* global
    CBConvert,
    CBModel,
    CBMessageMarkup,
    CBTest,

    CBConvertTests_valueAsMonikerTestCases,
    CBConvertTests_valueAsNameTestCases,
*/



var CBConvertTests = {

    /* -- tests -- -- -- -- -- */



    /**
     * @return object|Promise
     */
    CBTest_centsToDollars: function () {
        let tests = [
            [150, "1.50"],
            ["5", "0.05"],
            [75, "0.75"],
            ["  3500  ", "35.00"],
            ["  -5  ", "-0.05"],
            ["  -3500  ", "-35.00"],
        ];

        for (let i = 0; i < tests.length; i += 1) {
            let test = tests[i];
            let value = test[0];
            let actualResult = CBConvert.centsToDollars(value);
            let expectedResult = test[1];

            if (actualResult !== expectedResult) {
                let valueAsMessage = CBMessageMarkup.stringToMessage(
                    CBConvert.valueToPrettyJSON(value)
                );

                let actualResultAsMessage = CBMessageMarkup.stringToMessage(
                    CBConvert.valueToPrettyJSON(actualResult)
                );

                let expectedResultAsMessage = CBMessageMarkup.stringToMessage(
                    CBConvert.valueToPrettyJSON(expectedResult)
                );

                let message = `

                    When the value (${valueAsMessage} (code)) was used as an
                    argument to CBConvert.centsToDollars() the actual result was
                    (${actualResultAsMessage} (code)) but the expected result
                    was (${expectedResultAsMessage} (code)).

                `;

                return {
                    succeeded: false,
                    message: message,
                };
            }
        }

        return {
            succeeded: true,
        };
    },
    /* CBTest_centsToDollars() */



    /**
     * @return object|Promise
     */
    CBTest_dollarsAsCents: function () {
        let tests = [
            ["68.21", 6821],
            [" 68.21  ", 6821],
            ["000068.21", 6821],
            ["68.210000", 6821],
            ["-68.21", -6821],
            [" -68.21 ", -6821],
            [" -00068.21000 ", -6821],
            ["42", 4200],
            [" 42 ", 4200],
            [" 00042", 4200],
            [".42", 42],
            [" .42 ", 42],
            [".42000", 42],
            ["0.42", 42],
            [".42", 42],
            ["0", 0],

            ["68.211", undefined],
            [".211", undefined],
            ["a68.21", undefined],
            ["68.21a", undefined],
            ["", undefined],
            ["five", undefined],

            [21.22, 2122],
            [21, 2100],
            [0.22, 22],
            [-0.22, -22],

            [0.333, undefined],
            [true, undefined],
            [false, undefined],
        ];

        for (let i = 0; i < tests.length; i += 1) {
            let test = tests[i];
            let value = test[0];
            let actualResult = CBConvert.dollarsAsCents(value);
            let expectedResult = test[1];

            if (actualResult !== expectedResult) {
                let valueAsMessage = CBMessageMarkup.stringToMessage(
                    CBConvert.valueToPrettyJSON(value)
                );

                let actualResultAsMessage = CBMessageMarkup.stringToMessage(
                    CBConvert.valueToPrettyJSON(actualResult)
                );

                let expectedResultAsMessage = CBMessageMarkup.stringToMessage(
                    CBConvert.valueToPrettyJSON(expectedResult)
                );

                let message = `

                    When the value (${valueAsMessage} (code)) was used as an
                    argument to CBConvert.dollarsAsCents() the actual result was
                    (${actualResultAsMessage} (code)) but the expected result
                    was (${expectedResultAsMessage} (code)).

                `;

                return {
                    succeeded: false,
                    message: message,
                };
            }
        }

        return {
            succeeded: true,
        };
    },
    /* CBTest_dollarsAsCents() */



    /**
     * @return object
     */
    CBTest_stringToCleanLine: function () {
        let actualResult = CBConvert.stringToCleanLine(
            "   Hello.\n\nHow are you?\t\tI'm fine!\t  \n"
        );

        let expectedResult = "Hello. How are you? I'm fine!";

        if (actualResult !== expectedResult) {
            return CBTest.resultMismatchFailure(
                "test 1",
                actualResult,
                expectedResult
            );
        }

        return {
            succeeded: true,
        };
    },
    /* CBTest_stringToCleanLine() */



    /**
     * @return object|Promise
     */
    CBTest_stringToLines: function () {
        let testValue = "one\ntwo\rthree\r\nfour\rfive\nsix";
        let actualResult = CBConvert.stringToLines(testValue);
        let expectedResult = [
            "one",
            "two",
            "three",
            "four",
            "five",
            "six"
        ];

        if (!CBModel.equals(actualResult, expectedResult)) {
            return CBTest.resultMismatchFailure(
                'test 1',
                actualResult,
                expectedResult
            );
        } else {
            return {
                succeeded: true,
            };
        }
    },

    /**
     * @return object|Promise
     */
    CBTest_valueAsInt: function () {
        let tests = [
            [5, 5],
            [5.0, 5],
            [5.1, undefined],
            [-5, -5],
            [-5.0, -5],
            [-5.1, undefined],
            ["5", 5],
            [" 5 ", 5],
            ["5.0", 5],
            ["5.1", undefined],
            [" -5 ", -5],
            [" -5.0 ", -5],
            [" - 5.0 ", undefined],
            [" -5.1 ", undefined],
            ["", undefined],
            ["five", undefined],
            [true, undefined],
            [false, undefined],
        ];

        for (let i = 0; i < tests.length; i += 1) {
            let test = tests[i];
            let value = test[0];
            let actualResult = CBConvert.valueAsInt(value);
            let expectedResult = test[1];

            if (actualResult !== expectedResult) {
                let valueAsMessage = CBMessageMarkup.stringToMessage(
                    CBConvert.valueToPrettyJSON(value)
                );

                let actualResultAsMessage = CBMessageMarkup.stringToMessage(
                    CBConvert.valueToPrettyJSON(actualResult)
                );

                let expectedResultAsMessage = CBMessageMarkup.stringToMessage(
                    CBConvert.valueToPrettyJSON(expectedResult)
                );

                let message = `

                    When the value (${valueAsMessage} (code)) was used as an
                    argument to CBConvert.valueAsInt() the actual result was
                    (${actualResultAsMessage} (code)) but the expected result
                    was (${expectedResultAsMessage} (code)).

                `;

                return {
                    succeeded: false,
                    message: message,
                };
            }
        }

        return {
            succeeded: true,
        };
    },
    /* CBTest_valueAsInt() */



    /**
     * @return object|Promise
     */
    CBTest_valueAsModel: function () {
        let validModels = [
            {
                className: "CBViewPage",
            },
            {
                className: " ",
            },
            {
                className: " CBViewPage",
            },
            {
                className: "CBViewPage ",
            },
        ];

        for (let index = 0; index < validModels.length; index += 1) {
            let model = validModels[index];
            let actualResult = CBConvert.valueAsModel(model);
            let expectedResult = model;

            if (actualResult !== expectedResult) {
                return CBTest.resultMismatchFailure(
                    "valid model test index " + index + " failed",
                    actualResult,
                    expectedResult
                );
            }
        }

        let invalidModels = [
            2,
            5.5,
            "hello",
            [],
            {
                className: "",
            },
        ];

        for (let index = 0; index < invalidModels.length; index += 1) {
            let model = invalidModels[index];
            let actualResult = CBConvert.valueAsModel(model);
            let expectedResult; // = undefined;

            if (actualResult !== expectedResult) {
                return CBTest.resultMismatchFailure(
                    "invalid model test index " + index + " failed",
                    actualResult,
                    expectedResult
                );
            }
        }

        return {
            succeeded: true,
        };
    },
    /* CBTest_valueAsModel() */



    /**
     * @return object|Promise
     */
    CBTest_valueAsMoniker: function () {
        let testCaseCount = CBConvertTests_valueAsMonikerTestCases.length;

        for (let index = 0; index < testCaseCount; index += 1) {
            let testCase = CBConvertTests_valueAsMonikerTestCases[index];
            let actualResult = CBConvert.valueAsMoniker(testCase.originalValue);
            let expectedResult = (
                (testCase.expectedResult === null) ?
                undefined :
                testCase.expectedResult
            );

            if (actualResult !== expectedResult) {
                return CBTest.resultMismatchFailure(
                    JSON.stringify(testCase.originalValue),
                    actualResult,
                    expectedResult
                );
            }
        }

        return {
            succeeded: true,
        };
    },
    /* CBTest_valueAsMoniker() */



    /**
     * @return object
     */
    CBTest_valueAsName: function () {
        let testCaseCount = CBConvertTests_valueAsNameTestCases.length;

        for (let index = 0; index < testCaseCount; index += 1) {
            let testCase = CBConvertTests_valueAsNameTestCases[index];

            let actualResult = CBConvert.valueAsName(
                testCase.originalValue
            );

            let expectedResult = (
                testCase.expectedResult === null ?
                undefined :
                testCase.expectedResult
            );

            if (actualResult !== expectedResult) {
                return CBTest.resultMismatchFailure(
                    JSON.stringify(testCase.originalValue),
                    actualResult,
                    expectedResult
                );
            }
        }

        return {
            succeeded: true,
        };
    },
    /* CBTest_valueAsName() */



    /**
     * @return object|Promise
     */
    CBTest_valueAsNumber: function () {
        let tests = [
            [5, 5],
            [5.0, 5],
            [5.1, 5.1],
            ["5", 5],
            [" 5 ", 5],
            ["5.0", 5],
            ["5.1", 5.1],
            ["  3.14159  ", 3.14159],
            [" -3.14159  ", -3.14159],
            ["- 3.14159  ", undefined],
            ["", undefined],
            ["five", undefined],
            [true, undefined],
            [false, undefined],
            [Number.NaN, undefined],
            [Number.POSITIVE_INFINITY, undefined],
            [Number.NEGATIVE_INFINITY, undefined],
            [-0, 0],
            [function () { return 5; }, undefined],
            [{a: 1}, undefined],
        ];

        for (let i = 0; i < tests.length; i += 1) {
            let test = tests[i];
            let value = test[0];
            let actualResult = CBConvert.valueAsNumber(value);
            let expectedResult = test[1];

            if (actualResult !== expectedResult) {
                let valueAsMessage = CBMessageMarkup.stringToMessage(
                    CBConvert.valueToPrettyJSON(value)
                );

                let actualResultAsMessage = CBMessageMarkup.stringToMessage(
                    CBConvert.valueToPrettyJSON(actualResult)
                );

                let expectedResultAsMessage = CBMessageMarkup.stringToMessage(
                    CBConvert.valueToPrettyJSON(expectedResult)
                );

                let message = `

                    When the value (${valueAsMessage} (code)) was used as an
                    argument to CBConvert.valueAsNumber() the actual result was
                    (${actualResultAsMessage} (code)) but the expected result
                    was (${expectedResultAsMessage} (code)).

                `;

                return {
                    succeeded: false,
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
    CBTest_valueAsObject: function () {
        let tests = [
            [{a:1}, "same"],
            [{}, "same"],
            [undefined],
            [null],
            [1],
            ["1"],
            [true],
            [[1,2,3]],
        ];

        for (let i = 0; i < tests.length; i += 1) {
            let test = tests[i];
            let value = test[0];
            let actual = CBConvert.valueAsObject(value);
            let expected;

            if (test[1] === "same") {
                expected = value;
            }

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

    /**
     * @return object|Promise
     */
    CBTest_valueToBool: function () {
        let falsyValues = [
            false,
            0,
            0.0,
            null,
            undefined,
            "",
            " ",
            "          ",
            "\t",
            " \t ",
            "0",
            "    0",
            "0    ",
            "  0  ",
            "\t 0 \t",
            "\n 0 \n",
        ];

        for (let i = 0; i < falsyValues.length; i += 1) {
            let falsyValue = falsyValues[i];
            let actualResult = CBConvert.valueToBool(falsyValue);
            let expectedResult = false;

            if (actualResult !== expectedResult) {
                return CBTest.resultMismatchFailure(
                    CBConvert.valueToPrettyJSON(falsyValue),
                    actualResult,
                    expectedResult
                );
            }
        }

        let truthyValues = [
            true,
            1,
            1.0,
            "1",
            " 1 ",
            "\t 1 \t",
            "\n 1 \n",
            "a",
            Number.NaN,
            Number.POSITIVE_INFINITY,
            Number.NEGATIVE_INFINITY,
        ];

        for (let i = 0; i < truthyValues.length; i += 1) {
            let truthyValue = truthyValues[i];
            let actualResult = CBConvert.valueToBool(truthyValue);
            let expectedResult = true;

            if (actualResult !== expectedResult) {
                return CBTest.resultMismatchFailure(
                    CBConvert.valueToPrettyJSON(truthyValue),
                    actualResult,
                    expectedResult
                );
            }
        }

        return {
            succeeded: true,
        };
    },

    /**
     * @return object|Promise
     */
    CBTest_valueToObject: function () {
        let tests = [
            [{a:1}, "same"],
            [{}, "same"],
            [undefined],
            [null],
            [1],
            ["1"],
            [true],
            [[1,2,3]],
        ];

        for (let i = 0; i < tests.length; i += 1) {
            let test = tests[i];
            let value = test[0];
            let actual = CBConvert.valueToObject(value);

            if (test[1] === "same") {
                let expected = value;
                if (actual !== expected) {
                    return CBTest.resultMismatchFailure(
                        `Test index ${i}`,
                        actual,
                        expected
                    );
                }
            } else {
                let expected = {};

                if (!CBModel.equals(actual, expected)) {
                    return CBTest.resultMismatchFailure(
                        `Test index ${i}`,
                        actual,
                        expected
                    );
                }
            }
        }

        return {
            succeeded: true,
        };
    },
};
