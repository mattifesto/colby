"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBErrorHandler */
/* global
    CBException,
    CBUIPanel,
    Colby,
*/

var CBErrorHandler = {

    /**
     * @param Error error
     *
     * @return undefined
     */
    displayAndReport: function (error) {
        CBUIPanel.message = CBException.errorToExtendedMessage(error);
        CBUIPanel.isShowing = true;

        Colby.report(error);
    },
    /* displayAndReport() */
};
