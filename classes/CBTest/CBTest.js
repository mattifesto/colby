"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBTest */
/* global
    CBConvert,
    CBMessageMarkup,
    CBModel,
    Colby,
*/

var CBTest = {

    /* -- functions -- -- -- -- -- */

    /**
     * @param object args
     *
     * @return Promise -> object
     */
    runServerTest: function (args) {
        let testArgs = {
            className: CBModel.valueToString(args, "test.testClassName"),
            testName: CBModel.valueToString(args, "test.name"),
        };

        let promise = Colby.callAjaxFunction(
            "CBTest",
            "run",
            testArgs
        );

        return promise;
    },
    /* runServerTest() */


    /* -- test result creation functions -- -- -- -- -- */

    /**
     * @param string testTitle
     * @param string message
     *
     * @return object
     */
    generalFailure: function (testTitle, issueMessage) {
        let testTitleAsMessage = CBMessageMarkup.stringToMessage(
            testTitle
        );

        let message = `

            --- dl
                --- dt
                    test title
                ---
                --- dd
                    ${testTitleAsMessage}
                ---
                --- dt
                    issue
                ---
                --- dd
                    ${issueMessage}
                ---
            ---

        `;

        return {
            succeeded: false,
            message: message,
        };
    },

    /**
     * @param string testTitle
     * @param mixed actualResult
     * @param mixed expectedResult
     *
     * @return object
     */
    resultMismatchFailure: function (testTitle, actualResult, expectedResult) {
        let testTitleAsMessage = CBMessageMarkup.stringToMessage(
            testTitle
        );

        let actualResultAsJSONAsMessage = CBMessageMarkup.stringToMessage(
            CBConvert.valueToPrettyJSON(actualResult)
        );

        let expectedResultAsJSONAsMessage = CBMessageMarkup.stringToMessage(
            CBConvert.valueToPrettyJSON(expectedResult)
        );

        let message = `

            --- dl
                --- dt
                    test title
                ---
                --- dd
                    ${testTitleAsMessage}
                ---
                --- dt
                    actual result
                ---
                --- dd
                    --- pre\n${actualResultAsJSONAsMessage}
                    ---
                ---
                --- dt
                    expected result
                ---
                --- dd
                    --- pre\n${expectedResultAsJSONAsMessage}
                    ---
                ---
            ---

        `;

        return {
            succeeded: false,
            message: message,
        };
    },

    /**
     * @param string testTitle
     * @param mixed value
     * @param string message
     *
     * @return object
     */
    valueIssueFailure: function (testTitle, value, issueMessage) {
        let testTitleAsMessage = CBMessageMarkup.stringToMessage(
            testTitle
        );

        let valueAsJSONAsMessage = CBMessageMarkup.stringToMessage(
            CBConvert.valueToPrettyJSON(value)
        );

        let message = `

            --- dl
                --- dt
                    test title
                ---
                --- dd
                    ${testTitleAsMessage}
                ---
                --- dt
                    issue
                ---
                --- dd
                    ${issueMessage}
                ---
                --- dt
                    value
                ---
                --- dd
                    --- pre\n${valueAsJSONAsMessage}
                    ---
                ---
            ---

        `;

        return {
            succeeded: false,
            message: message,
        };
    },
    /* valueIssueFailure() */
};
/* CBTest */
