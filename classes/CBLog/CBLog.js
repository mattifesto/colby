"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBLog */
/* global
    CBAjax,
*/

var CBLog = {

    /**
     * @param ID? processID
     *
     * @return Promise -> int
     */
    fetchMostRecentSerial: function (processID) {
        return CBAjax.call(
            "CBLog",
            "fetchMostRecentSerial",
            {
                processID: processID,
            }
        );
    }
};
