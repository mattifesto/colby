"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBUser */
/* global
    CBErrorHandler,
    Colby,
*/



var CBUser = {

    /**
     * @return Promise -> undefined
     */
    signOut() {
        let promise = Colby.callAjaxFunction(
            "CBUser",
            "signOut"
        ).catch(
            function (error) {
                CBErrorHandler.report(error);
            }
        );

        return promise;
    },
    /* signOut() */



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
