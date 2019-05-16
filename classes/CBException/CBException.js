"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBException */
/* global
    Colby,
*/

var CBException = {

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
};
