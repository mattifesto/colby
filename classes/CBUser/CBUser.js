"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBUser */

var CBUser = {

    /**
     * @param string userID
     *
     * @return string
     */
    userIDToUserAdminPageURL: function (userID) {
        return (
            "/admin/?" +
            "c=CBAdminPageForUserSettings&" +
            "hash=" +
            userID
        );
    },
    /* userIDToUserAdminPageURL() */
};
