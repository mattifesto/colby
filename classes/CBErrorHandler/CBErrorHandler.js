"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBErrorHandler */
/* global
    CBAjax,
    console,
*/



/**
 * @NOTE 2020_02_28
 *
 *      Error handling functions should move here from other classes.
 *
 * @NOTE 2020_04_16
 *
 *      This class should not have any user interface functionality.
 */
(function () {

    /**
     * @NOTE 2019_06_14
     *
     *      Browsers must natively support Promises. This requirement makes
     *      Internet Explorer 11 an unsupported browser.
     *
     * @NOTE 2020_11_26
     *
     *      The currentBrowserIsSupported functionality has been moving slowly
     *      to lower level classes. It may seem a bit odd here, but this is the
     *      lowest level class that requires the functionality. If it becomes
     *      necessary in the future, this functionality could move to a new
     *      class that focuses on browser issues, maybe with a name like
     *      CBBrowser.
     */
    let currentBrowserIsSupported = false;

    if (
        typeof Promise !== "undefined" &&
        Promise.toString().indexOf("[native code]") !== -1
    ) {
        currentBrowserIsSupported = true;
    } else {
        window.alert(
            "The web browser you are using is no longer supported by this " +
            "website. Use a recent version a regularly maintained browser " +
            "such as Chrome, Edge, Firefox, or Safari."
        );
    }



    window.CBErrorHandler = {
        errorToCBJavaScriptErrorModel,
        getCurrentBrowserIsSupported,
        report,

        /**
         * @deprecated 2020_11_27
         *
         *      This property has been replaced by
         *      getCurrentBrowserIsSupported() because functions are better than
         *      properties in situation like this because accessing the wrong
         *      propery name will return undefined but accessing the wrong
         *      function will produce an error. We want to make sure typos will
         *      produce an error.
         */
        get currentBrowserIsSupported() {
            return currentBrowserIsSupported;
        },
    };



    /**
     * Converts an error object to a CBJavaScriptError model.
     *
     * Properties:
     *
     *      Safari          Firefox         Chrome
     *      ------          -------         ------
     *      column          columnNumber    no
     *      line            lineNumber      no
     *      sourceURL       fileName        no
     *
     * History:
     *
     *      An initial goal was to stringify and Error object and send it to an
     *      ajax function. But when an Error object is stringified it doesn't
     *      serialize all of its properties.
     *
     *      Additional information that is not contained in the Error object is
     *      added to the model returned by this function.
     *
     *      The ErrorEvent object passed to the listener of the "error" event
     *      has some standardized properties that are similar, but not all
     *      errors are handled by an error event listener. The "stack" property
     *      actually contains all the data but has a different format on Chrome
     *      browsers.
     *
     * @param Error error
     *
     * @return object (CBJavaScriptError)
     */
    function errorToCBJavaScriptErrorModel(
        error
    ) {
        let errorDetails = errorToErrorDetails(
            error
        );

        return {
            className: 'CBJavaScriptError',
            column: errorDetails.columnNumber,
            line: errorDetails.lineNumber,
            message: error.message,
            pageURL: location.href,
            sourceURL: errorDetails.sourceURL,
            stack: error.stack,
        };



        /* -- closures -- -- -- -- -- */



        /**
         * @param Error error
         *
         * @return object
         *
         *      {
         *          sourceURL: string
         *          lineNumber: int
         *          columnNumber: int
         *      }
         */
        function
        errorToErrorDetails(
            error
        ) {
            let errorDetails = {};

            /* Safari */
            if (
                error.line !== undefined
            ) {
                errorDetails.sourceURL = error.sourceURL;
                errorDetails.lineNumber = error.line;
                errorDetails.columnNumber = error.column;
            }

            /* Firefox */
            else if (
                error.lineNumber !== undefined
            ) {
                errorDetails.sourceURL = error.fileName;
                errorDetails.lineNumber = error.lineNumber;
                errorDetails.columnNumber = error.columnNumber;
            }

            /* Chrome */
            else if (
                typeof error.stack === "string"
            ) {
                let stackLines = error.stack.split(
                    "\n"
                );

                /**
                 * The first line is the error message, the second is the code
                 * location.
                 */
                if (
                    stackLines.length < 2
                ) {
                    return errorDetails;
                }

                let stackLine = stackLines[1];

                let matches = stackLine.match(
                    /\s*at (.*) \((.+):([0-9]+):([0-9]+)\)$/
                );

                if (
                    matches !== null
                ) {
                    errorDetails.sourceURL = matches[2];
                    errorDetails.lineNumber = matches[3];
                    errorDetails.columnNumber = matches[4];
                }
            }

            return errorDetails;
        }
        /* errorToErrorDetails() */

    }
    /* errorToCBJavaScriptErrorModel() */



    /**
     * @return bool
     */
    function
    getCurrentBrowserIsSupported() {
        return currentBrowserIsSupported;
    }
    /* getCurrentBrowserIsSupported() */


    /**
     * Use this function to report an error to the server.
     *
     *      callAjaxFunction(
     *          "class",
     *          "function"
     *      ).catch(
     *          function (error) {
     *              CBErrorHandler.report(error);
     *          }
     *      );
     *
     * This function will filter out errors created in response to a failed Ajax
     * request because the server generated and previously logged those errors
     * during the request.
     *
     * @param Error error
     *
     * @return Promise -> undefined
     *
     *      This function returns a promise that will resolve when the request
     *      to the server to report the error has completed. This is generally
     *      not an important promise but may be important in cases where you
     *      want to report the error and wait to navigate to another page so you
     *      don't cancal the report request to the server.
     */
   function
   report(
       error
   ) {
       if (!currentBrowserIsSupported) {
           return;
       }

       if (error.ajaxResponse) { // Filter out Ajax errors
           return;
       }

       let errorModel = errorToCBJavaScriptErrorModel(
           error
       );

       let promise = CBAjax.call(
           "CBJavaScript",
           "reportError",
           {
               errorModel,
           }
       ).catch(
           function (error) {
               console.log(
                   "CBErrorHandler.reportError() Ajax request failed."
               );

               console.log(
                   error.message
               );
           }
       );

       return promise;
   }
   /* report() */

})();
