/* global
    CBAjax,
    CBConvert,

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
    "use strict";

    /**
     * @NOTE 2019_06_14
     *
     *      Browsers must natively support Promises. This requirement makes
     *      Internet Explorer 11 an unsupported browser.
     *
     * @NOTE 2022_01_29
     *
     *      The currentBrowserIsSupported functionality has been moving slowly
     *      to lower level classes. Today the CBJavaScript class was decided to
     *      be the most base level JavaScript class and this should probably go
     *      there if it is moved. However, I don't know that this code is even
     *      necessary anymore. Something to consider.
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
        errorToCBJavaScriptErrorModel, /* deprecated */
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
     * @deprecated 2022_02_06
     *
     *      Use CBConvert.errorToCBJavaScriptErrorModel()
     */
    function errorToCBJavaScriptErrorModel(
        error
    ) {
        return CBConvert.errorToCBJavaScriptErrorModel(
            error
        ) ;
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
