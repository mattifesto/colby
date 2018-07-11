"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBModelTests */
/* global
    CBMessageMarkup,
    CBModel,
*/

var CBModelTests = {

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
        ];

        for (let i = 0; i < equalValues.length; i += 1) {
            let values = equalValues[i];

            if (!CBModel.equals(values[0], values[1])) {
                let valuesAsMessage = CBMessageMarkup.stringToMessage(
                    JSON.stringify(values)
                );
                let message = `

                    CBModel.equal() was expected to consider the following values were expected to be equal.

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
};
