"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBException */
/* global
    CBConvert,
    CBMessageMarkup,
    CBModel,
    Colby,
*/

var CBException = {

    /**
     * @return string
     */
    errorToExtendedMessage: function (error) {
        let extendedMessage = CBModel.valueToString(
            error,
            'CBException.extendedMessage'
        );

        if (extendedMessage === "") {
            return CBMessageMarkup.stringToMessage(
                CBModel.valueToString(
                    error,
                    "message"
                )
            );
        }

        return extendedMessage;
    },
    /* errorToExtendedMessage() */



    /**
     * @param Error error
     *
     * @return CBID|undefined
     */
    errorToSourceCBID: function (error) {
        return CBModel.valueAsCBID(
            error,
            "ajaxResponse.sourceCBID"
        );
    },



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
     *      information is created with respect to excactly where the error
     *      object is created so the error object shouldn't be created in this
     *      function and file.
     *
     *      A secondary reason is that this allows the caller to create
     *      whichever specific type of error object they want to use.
     *
     * @param string message
     * @param ID sourceID
     *
     * @return Error
     */
    withError: function (error, extendedMessage, sourceID) {
        if (error.CBException !== undefined) {
            Colby.reportError(
                Error(
                    [
                        "CBException.withError() was call with an error that",
                        "is already a CBException",
                    ].join(" ")
                )
            );

            return;
        }

        let pod = {
            get extendedMessage() {
                return extendedMessage;
            },
            get sourceID() {
                return sourceID;
            },
        };

        Object.defineProperty(
            error,
            "CBException",
            {
                configurable: true,
                enumerable: false,
                value: pod,
                writable: false,
            }
        );

        return error;
    },
    /* withError() */


    /**
     * @param Error error
     * @param mixed value
     * @param string sourceID
     *
     * @return Error
     */
    withValueRelatedError: function (error, value, sourceID) {
        let messageAsMessage = CBMessageMarkup.stringToMessage(
            error.message
        );

        let valueAsMessage = CBMessageMarkup.stringToMessage(
            CBConvert.valueToPrettyJSON(value)
        );

        let extendedMessage = `

            ${messageAsMessage}

            --- pre\n${valueAsMessage}
            ---

        `;

        return CBException.withError(
            error,
            extendedMessage,
            sourceID
        );
    },
    /* withValueRelatedError() */
};
