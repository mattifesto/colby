"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBErrorHandler */
/* global
    Colby,
*/



/**
 * @NOTE 2020_02_28
 *
 *      Error handling functions should move here from other classes.
 *
 * @NOTE 2020_04_16
 *
 *      This class should not have any user interface functionality.
 *
 * @NOTE 2020_04_25, 2020_07_06
 *
 *      The plan:
 *
 *      1. This class will require CBAjax.
 *
 *      2. The Colby.js error reporting code will be moved to this class and
 *      Colby.js functions will call it while its functions are deprecated.
 *
 *      2a. Colby.browserIsSupported functionality will move to this class or
 *      a class something like CBBrowser.
 */

(function () {

    window.CBErrorHandler = {
        report,
    };

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
       return Colby.reportError(
           error
       );
   }
   /* report() */

})();
