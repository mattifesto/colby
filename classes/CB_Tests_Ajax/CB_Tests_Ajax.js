/* global
    CBAjax,
    CBException,
*/


(function ()
{
    window.CB_Tests_Ajax =
    {
        interfaceHasNotBeenImplemented:
        CB_Tests_Ajax_interfaceHasNotBeenImplemented,
    };



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
