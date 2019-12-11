"use strict";
/* jshint strict: global */
/* jshint esversion: 8 */
/* exported CBPagesTrashAdmin_Tests */
/* global
    CBException,
    CBTest,
    Colby,
*/



var CBPagesTrashAdmin_Tests = {

    async CBTest_ajax() {
        {
            let testName = "fetchPages";

            let actualSourceCBID = "initial";
            let expectedSourceCBID = "initial";

            await Colby.callAjaxFunction(
                "CBPagesTrashAdmin",
                "fetchPages"
            ).catch(
                function (error) {
                    actualSourceCBID = CBException.errorToSourceCBID(error);
                }
            );

            if (actualSourceCBID !== expectedSourceCBID) {
                return CBTest.resultMismatchFailure(
                    testName,
                    actualSourceCBID,
                    expectedSourceCBID
                );
            }
        }

        {
            let testName = "recoverPage";
            let testPageModelCBID = "09e0a3527deb3dde49eb0453371cd0b454b4e505";

            let actualSourceCBID = "initial";
            let expectedSourceCBID = "initial";

            await Colby.callAjaxFunction(
                "CBPagesTrashAdmin",
                "recoverPage",
                {
                    pageID: testPageModelCBID,
                }
            ).catch(
                function (error) {
                    actualSourceCBID = CBException.errorToSourceCBID(error);
                }
            );

            if (actualSourceCBID !== expectedSourceCBID) {
                return CBTest.resultMismatchFailure(
                    testName,
                    actualSourceCBID,
                    expectedSourceCBID
                );
            }
        }

        return {
            succeeded: true,
        };
    }
    /* CBTest_ajax() */

};
