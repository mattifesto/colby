/* global
    CBAjax,
    CBErrorHandler,
*/


(function () {
    "use strict";

    let publicProfilesByUserModelCBID = {};



    window.CBUser = {
        fetchPublicProfileByUserModelCBID,
        signOut,
        userIDToUserAdminPageURL,
    };



    /**
     * @param CBID userModelCBID
     *
     * @return Promise -> object
     */
    async function
    fetchPublicProfileByUserModelCBID(
        userModelCBID
    ) {
        if (
            publicProfilesByUserModelCBID[userModelCBID] === undefined
        ) {
            let publicProfile = await CBAjax.call(
                "CBUser",
                "fetchPublicProfileByUserModelCBID",
                {
                    userModelCBID
                }
            );

            publicProfilesByUserModelCBID[userModelCBID] = Object.freeze(
                publicProfile
            );

            return publicProfile;
        } else {
            return publicProfilesByUserModelCBID[userModelCBID];
        }
    }
    /* fetchPublicProfileByUserModelCBID() */



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
