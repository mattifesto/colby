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
