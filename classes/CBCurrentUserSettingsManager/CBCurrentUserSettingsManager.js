"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* global
    CBErrorHandler,
    CBUI,
    CBUser,
*/



(function () {

    /* public API */

    window.CBCurrentUserSettingsManager = {
        CBUserSettingsManager_createElement,
    };



    /**
     * @param object args
     *
     *      {
     *          targetUserCBID: CBID
     *      }
     *
     * @return Element
     */
    function CBUserSettingsManager_createElement(
        /* args */
    ) {
        let elements = CBUI.createElementTree(
            "CBCurrentUserSettingsManager",
            "CBUI_container1",
            "CBUI_button1"
        );

        let buttonElement = elements[2];
        buttonElement.textContent = "Sign Out";

        buttonElement.addEventListener(
            "click",
            function () {
                CBUser.signOut().then(
                    function () {
                        window.location.reload();
                    }
                ).catch(
                    function (error) {
                        CBErrorHandler.report(error);
                    }
                );
            }
        );

        return elements[0];
    }
    /* CBUserSettingsManager_createElement() */

})();
