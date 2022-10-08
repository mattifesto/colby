/* global
    CBModel,
*/


(function ()
{
    "use strict";



    let CBCodeSearch =
    {
        getCBMessage:
        CBCodeSearch_getCBMessage,
    };

    window.CBCodeSearch =
    CBCodeSearch;



    /**
     * @param object codeSearchModelArgument
     *
     * @return string
     */
    function
    CBCodeSearch_getCBMessage(
        codeSearchModelArgument
    ) // -> string
    {
        let cbmessage;

        if (
            codeSearchModelArgument.CBCodeSearch_cbmessage_property !==
            undefined
        ) {
            cbmessage =
            CBModel.valueToString(
                codeSearchModelArgument,
                "CBCodeSearch_cbmessage_property"
            );
        }
        // if

        else
        {
            cbmessage =
            CBModel.valueToString(
                codeSearchModelArgument,
                "cbmessage"
            );
        }
        // else

        return cbmessage;
    }
    // CBCodeSearch_getCBMessage()



}
)();
