"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBErrorHandler */
/* global
    CBUIPanel,
    Colby,
*/



/**
 * @NOTE 2020_02_28
 *
 * Error handling functions should move here from other classes.
 */
var CBErrorHandler = {

    /**
     * This function is currently approved to replace
     * Colby.displayAndReportError() as a start to move error handling out of
     * the Colby.js file.
     *
     * Example:
     *
     *      Colby.callAjaxFunction(
     *          "MyClass",
     *          "MyFunction"
     *      ).then(
     *          function (value) { ... }
     *      ).catch(
     *          function (error) {
     *              CBErrorHandler.displayAndReport(error);
     *          }
     *      );
     *
     * @param Error error
     *
     * @return undefined
     */
    displayAndReport: function (error) {
        CBUIPanel.displayError(error);
        Colby.reportError(error);
    },
    /* displayAndReport() */



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
    report(error) {
        return Colby.reportError(error);
    },
    /* report() */

};
