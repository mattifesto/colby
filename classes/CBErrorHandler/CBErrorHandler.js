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
 * It's uncertain if this class should exist. CBUIPanel has a displayError()
 * function and maybe that code should be moved into this class.
 *
 * Should Colby.reportError() also be moved into this class?
 */
var CBErrorHandler = {

    /**
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
