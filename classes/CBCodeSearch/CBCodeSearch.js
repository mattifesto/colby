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

        getSeverity:
        CBCodeSearch_getSeverity,
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



    /**
     * @param object codeSearchModelArgument
     *
     * @return string
     */
    function
    CBCodeSearch_getSeverity(
        codeSearchModelArgument
    ) // -> int
    {
        let severityValue;

        if (
            codeSearchModelArgument.CBCodeSearch_severity_property !==
            undefined
        ) {
            severityValue =
            CBModel.valueAsInt(
                codeSearchModelArgument,
                "CBCodeSearch_severity_property"
            );
        }

        else
        {
            severityValue =
            CBModel.valueAsInt(
                codeSearchModelArgument,
                "severity"
            );
        }

        if (
            severityValue ===
            undefined
        ) {
            severityValue =
            3;
        }

        return severityValue;
    }
    // CBCodeSearch_getSeverity()

}
)();
