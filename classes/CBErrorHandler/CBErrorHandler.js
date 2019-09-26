"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBErrorHandler */
/* global
    CBUIPanel,
    Colby,
*/


/**
 * @NOTE 2019_09_26
 *
 * This function is currently approved to replace Colby.displayAndReportError()
 * as a start to move error handling out of the Colby.js file.
 *
 * It's uncertain if this class should exist. CBUIPanel has a displayError()
 * function and maybe that code should be moved into this class.
 *
 * Should Colby.reportError() also be moved into this class?
 */
var CBErrorHandler = {

    /**
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
};
