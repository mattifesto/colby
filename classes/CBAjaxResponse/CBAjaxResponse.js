/* global
    CBModel,
*/


(function()
{
    "use strict";



    window.CBAjaxResponse =
    {
        fromError:
        CBAjaxResponse_fromError,

        getCBMessage:
        CBAjaxResponse_getCBMessage,
    };



    /**
     * @param Error errorArgument
     *
     * @return object|undefined
     */
    function
    CBAjaxResponse_fromError(
        errorArgument
    ) // -> object|undefined
    {
        let ajaxResponse =
        errorArgument.ajaxResponse;

        return ajaxResponse;
    }
    // CBAjaxResponse_fromError()



    /**
     * @param {object} ajaxResponseModelArgument
     *
     * @return {string}
     */
    function
    CBAjaxResponse_getCBMessage(
        ajaxResponseModelArgument
    ) // -> string
    {
        let cbmessage =
        CBModel.valueToString(
            ajaxResponseModelArgument,
            'CBAjaxResponse_cbmessage_property'
        );

        return cbmessage;
    }
    // CBAjaxResponse_getCBMessage()

}
)();
