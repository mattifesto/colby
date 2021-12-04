/* global
    CBAjax,
    CBErrorHandler,
*/


(function () {
    "use strict";

    window.CBUser = {
        signOut,
        userIDToUserAdminPageURL,
    };



    /**
     * @return Promise -> undefined
     */
    async function
    signOut(
    ) {
        try {
            await CBAjax.call(
                "CBUser",
                "signOut"
            );
        } catch (
            error
        ) {
            CBErrorHandler.report(
                error
            );
        }
    }
    /* signOut() */



    /**
     * @param string userID
     *
     * @return string
     */
    function
    userIDToUserAdminPageURL(
        userID
    ) {
        return (
            "/admin/?" +
            "c=CBAdminPageForUserSettings&" +
            "hash=" +
            userID
        );
    }
    /* userIDToUserAdminPageURL() */

})();
