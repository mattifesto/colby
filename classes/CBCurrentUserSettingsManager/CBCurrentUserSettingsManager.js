"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* global
    CBUI,

    CBCurrentUserSettingsManager_signoutURL,
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
                window.location.href = CBCurrentUserSettingsManager_signoutURL;
            }
        );

        return elements[0];
    }
    /* CBUserSettingsManager_createElement() */

})();
