"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBTest */
/* global
    CBConvert,
    CBMessageMarkup,
*/

var CBTest = {

    /**
     * @param string testTitle
     * @param mixed actualResult
     * @param mixed expectedResult
     *
     * @return object
     */
    resultMismatchFailure: function (testTitle, actualResult, exprectedResult) {
        let testTitleAsMessage = CBMessageMarkup.stringToMessage(
            testTitle
        );

        let actualResultAsJSONAsMessage = CBMessageMarkup.stringToMessage(
            CBConvert.valueToPrettyJSON(actualResult)
        );

        let expectedResultAsJSONAsMessage = CBMessageMarkup.stringToMessage(
            CBConvert.valueToPrettyJSON(exprectedResult)
        );

        let message = `

            (test title (strong))

            ${testTitleAsMessage}

            (actual result (strong))

            --- pre\n${actualResultAsJSONAsMessage}
            ---

            (expected result (strong))

            --- pre\n${expectedResultAsJSONAsMessage}
            ---

        `;

        return {
            succeeded: false,
            message: message,
        };
    },
};
