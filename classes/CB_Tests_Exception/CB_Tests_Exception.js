/* global
    CBException,
    CBTest,
*/


(function () {
    "use strict";

    window.CB_Tests_Exception = {
        CBTest_errorToOneLineErrorReport,
    };



    /**
     * @return object
     */
    function
    CBTest_errorToOneLineErrorReport(
    ) {
        let testName = "test 1";
        let actualResult = "(no error thrown)";

        try {
            throw CBException.withError(
                Error(
                    "bad stuff happened"
                ),
                "",
                "579c9a3efc7256f4397eb7f56653c3bf465ca6d1"
            );
        } catch (
            error
        ) {
            actualResult = CBException.errorToOneLineErrorReport(
                error
            );
        }

        let regularExpression = (
            `^"bad stuff happened"` +
            ` in https?://` +
            `[^/]+` +
            `/colby/classes/CB_Tests_Exception/CB_Tests_Exception.v?` +
            `[0-9._]+` +
            `js line ` +
            `[0-9]+` +
            `$`
        );

        let matches = actualResult.match(
            RegExp(
                regularExpression
            )
        );


        if (
            matches === null
        ) {
            return CBTest.resultMismatchFailure(
                testName,
                actualResult,
                regularExpression
            );
        }

        return {
            succeeded: true,
        };
    }
    /* CBTest_errorToOneLineErrorReport() */

})();
