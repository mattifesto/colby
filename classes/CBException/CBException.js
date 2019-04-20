"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBException */
/* global
    Colby,
*/

var CBException = {

    /**
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
            extendedMessage: extendedMessage,
            sourceID: sourceID,
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
