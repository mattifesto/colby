"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBAdmin */
/* global
    Colby */

var CBAdmin = {

    /**
     * @return undefined
     */
    init: function () {
        Colby.warnOlderBrowsers();
    }
};

Colby.afterDOMContentLoaded(CBAdmin.init);
