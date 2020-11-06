"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBUser */
/* global
    CBAjax,
    CBErrorHandler,
*/



var CBUser = {

    /**
     * @return Promise -> undefined
     */
    signOut() {
        let promise = CBAjax.call(
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
