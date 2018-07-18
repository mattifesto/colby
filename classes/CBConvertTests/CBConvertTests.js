"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBConvertTests */
/* global
    CBConvert,
    CBModel,
    CBMessageMarkup,
*/

var CBConvertTests = {

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
            [" -5.0 ", -50],
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
    CBTest_valueToObject: function () {
        let tests = [
            [{a:1}, "same"],
            [{}, "same"],
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
                if (actual !== value) {
                    let valueAsMessage = CBMessageMarkup.stringToMessage(
                        CBConvert.valueToPrettyJSON(value)
                    );

                    let actualAsMessage = CBMessageMarkup.stringToMessage(
                        CBConvert.valueToPrettyJSON(actual)
                    );

                    let message = `

                        The following value was provided as an argument to
                        CBConvert.valueToObject()

                        --- pre\n${valueAsMessage}
                        ---

                        The return value was expected to be the exact argument
                        value but instead was

                        --- pre\n${actualAsMessage}
                        ---

                    `;

                    return {
                        succeeded: false,
                        message: message,
                    };
                }
            } else {
                let expected = {};

                if (!CBModel.equals(actual, expected)) {
                    let valueAsMessage = CBMessageMarkup.stringToMessage(
                        CBConvert.valueToPrettyJSON(value)
                    );

                    let actualAsMessage = CBMessageMarkup.stringToMessage(
                        CBConvert.valueToPrettyJSON(actual)
                    );

                    let message = `

                        The following value was provided as an argument to
                        CBConvert.valueToObject()

                        --- pre\n${valueAsMessage}
                        ---

                        The return value was expected to be an empty object but
                        instead was

                        --- pre\n${actualAsMessage}
                        ---

                    `;

                    return {
                        succeeded: false,
                        message: message,
                    };
                }
            }
        }

        return {
            succeeded: true,
        };
    },
};
