/* global
    CBAjax,
    CBAjaxResponse,
    CBConvert,
    CBException,
    CBTest,
*/


(function ()
{
    "use strict";



    window.CB_Tests_Ajax =
    {
        checkErrorAjaxResponseProperties:
        CB_Tests_Ajax_checkErrorAjaxResponseProperties,

        interfaceHasNotBeenImplemented:
        CB_Tests_Ajax_interfaceHasNotBeenImplemented,
    };



    /**
     * @return Promise -> undefined
     */
    async function
    CB_Tests_Ajax_checkErrorAjaxResponseProperties(
    ) // Promise -> object|undefined
    {
        try
        {
            await
            CBAjax.call2(
                "CB_Ajax_TestFunctionThatAlwaysProducesAnError"
            );
        }

        catch (
            error
        ) {
            let ajaxResponse =
            CBAjaxResponse.fromError(
                error
            );

            if (
                ajaxResponse === undefined
            ) {
                throw CBException.withError(
                    error,
                    "The error object does not have an ajax response.",
                    "ec156c06bc4b2e316f3405fbe835a4531acec182"
                );
            }

            let actualCBMessage =
            CBAjaxResponse.getCBMessage(
                ajaxResponse
            );

            let expectedCBMessage =
            "(cbmessage (b)) exception message";

            if (
                actualCBMessage !==
                expectedCBMessage
            ) {
                let title =
                CBConvert.stringToCleanLine(`

                    The actual ajax response cbmessage does not match the
                    expected ajax response cbmessage.

                `);

                return CBTest.resultMismatchFailure(
                    title,
                    actualCBMessage,
                    expectedCBMessage
                );
            }
        }
    }
    // CB_Tests_Ajax_checkErrorAjaxResponseProperties()



    /**
     * @return Promise -> undefined
     */
    async function
    CB_Tests_Ajax_interfaceHasNotBeenImplemented(
    ) // -> Promise -> undefined
    {
        try
        {
            await
            CBAjax.call(
                "CB_Tests_Ajax",
                "interfaceHasNotBeenImplemented",
            );
        }

        catch (
            error
        ) {
            let sourceCBID =
            CBException.errorToSourceCBID(
                error
            );

            if (
                sourceCBID !== "35ea28899f1335170ec7ec9b42a134c875037a5f"
            ) {
                throw error;
            }
        }
    }
    // CB_Tests_Ajax_undefinedInterfaceName()

}
)();
