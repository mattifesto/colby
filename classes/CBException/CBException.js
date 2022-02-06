/* global
    CBConvert,
    CBMessageMarkup,
*/


(function () {
    "use strict";

    window.CBException = {
        errorToExtendedMessage,
        errorToOneLineErrorReport: CBException_errorToOneLineErrorReport,
        errorToSourceCBID,
        withError,
        withValueRelatedError,
    };



    /**
     * @param Error error
     *
     * @return string (text)
     */
    function
    CBException_errorToOneLineErrorReport(
        error
    ) {
        let javascriptErrorModel = CBConvert.errorToCBJavaScriptErrorModel(
            error
        );

        return CBConvert.stringToCleanLine(`
            "${javascriptErrorModel.message}"
            in
            ${javascriptErrorModel.sourceURL}
            line
            ${javascriptErrorModel.line}
        `);
    }
    /* CBException_errorToOneLineErrorReport() */



    /**
     * @param Error error
     *
     * @return cbmessage
     */
    function
    errorToExtendedMessage(
        error
    ) {
        let cbmessage = "";

        if (error.CBException) {
            cbmessage = CBConvert.valueToString(
                error.CBException.extendedMessage
            );
        }

        if (cbmessage === "") {
            return CBMessageMarkup.stringToMessage(
                CBConvert.valueToString(
                    error.message
                )
            );
        }

        return cbmessage;
    }
    /* errorToExtendedMessage() */



    /**
     * @param Error error
     *
     * @return CBID|undefined
     */
    function
    errorToSourceCBID(
        error
    ) {
        let sourceCBID;

        if (error.CBException) {
            sourceCBID = CBConvert.valueAsCBID(
                error.CBException.sourceCBID
            );
        }

        return sourceCBID;
    }
    /* errorToSourceCBID() */



    /**
     * This function adds a CBException pod to an Error object. It can be used
     * in the following manner:
     *
     *      if (failure) {
     *          throw CBException.withError(
     *              Error(
     *                  "A number somewhere was too low."
     *              ),
     *              `
     *                  --- blockquote
     *                  Four score and seven years ago...
     *                  a number was too low.
     *                  ---
     *              `,
     *              "bc19df51de08f0b2838825f23ee424454805dfb2"
     *          );
     *      }
     *
     * @param Error error
     *
     *      The reason the error is passed as a parameter is that the error
     *      information is created with respect to exactly where the error
     *      object is created so the error object shouldn't be created in this
     *      function and file.
     *
     *      A secondary reason is that this allows the caller to create
     *      whichever specific type of error object they want to use.
     *
     * @param string cbmessage
     * @param CBID sourceCBID
     *
     * @return Error
     */
    function
    withError(
        error,
        cbmessage,
        sourceCBID
    ) {
        let api = {
            get extendedMessage() {
                return cbmessage;
            },

            /**
             * @deprecated 2020_09_26
             *
             *      Use sourceCBID.
             */
            get sourceID() {
                return sourceCBID;
            },

            get sourceCBID() {
                return sourceCBID;
            },
        };

        Object.defineProperty(
            error,
            "CBException",
            {
                configurable: true,
                enumerable: false,
                value: api,
                writable: false,
            }
        );

        return error;
    }
    /* withError() */



    /**
     * @param Error error
     * @param mixed value
     * @param CBID sourceCBID
     *
     * @return Error
     */
    function
    withValueRelatedError(
        error,
        value,
        sourceCBID
    ) {
        let messageAsMessage = CBMessageMarkup.stringToMessage(
            error.message
        );

        let valueAsMessage = CBMessageMarkup.stringToMessage(
            CBConvert.valueToPrettyJSON(value)
        );

        let cbmessage = `

            ${messageAsMessage}

            --- pre\n${valueAsMessage}
            ---

        `;

        return withError(
            error,
            cbmessage,
            sourceCBID
        );
    }
    /* withValueRelatedError() */

})();
