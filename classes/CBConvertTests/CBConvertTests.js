"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBConvertTests */
/* global
    CBConvert,
    CBModel,
    CBMessageMarkup,
    CBTest,
*/

var CBConvertTests = {

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
