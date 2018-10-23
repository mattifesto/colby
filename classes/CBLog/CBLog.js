"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBLog */
/* global
    Colby,
*/

var CBLog = {

    /**
     * @param ID? processID
     *
     * @return Promise -> int
     */
    fetchMostRecentSerial: function (processID) {
        return Colby.callAjaxFunction(
            "CBLog",
            "fetchMostRecentSerial",
            {
                processID: processID,
            }
        );
    }
};
