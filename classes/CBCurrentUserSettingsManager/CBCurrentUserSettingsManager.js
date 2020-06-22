"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* global
    CBConvert,
    CBErrorHandler,
    CBUI,
    CBUser,

    CBCurrentUserSettingsManager_currentUserEmailAddress,
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
        let element = CBUI.createElement(
            "CBCurrentUserSettingsManager"
        );

        {
            let currentUserEmailAddress = CBConvert.valueAsEmail(
                CBCurrentUserSettingsManager_currentUserEmailAddress
            );

            if (currentUserEmailAddress !== undefined) {
                element.appendChild(
                    createChangePasswordButtonElement()
                );
            }
        }

        element.appendChild(
            createSignOutButtonElement()
        );

        return element;
    }
    /* CBUserSettingsManager_createElement() */



    /**
     * @return Element
     */
    function createChangePasswordButtonElement() {
        let elements = CBUI.createElementTree(
            "CBUI_container1",
            [
                "CBUI_button1",
                "a",
            ]
        );

        let buttonElement = elements[1];
        buttonElement.textContent = "Change Password";
        buttonElement.href = (
            "/colby/user/reset-password/?userEmailAddress=" +
            encodeURIComponent(
                CBCurrentUserSettingsManager_currentUserEmailAddress
            )
        );

        return elements[0];
    }
    /* createChangePasswordButtonElement() */



    /**
     * @return Element
     */
    function createSignOutButtonElement() {
        let elements = CBUI.createElementTree(
            "CBUI_container1",
            "CBUI_button1"
        );

        let buttonElement = elements[1];
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
    /* createSignOutButtonElement() */

})();
