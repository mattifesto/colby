"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* global
    CBErrorHandler,
    CBUI,
    CBUser,
    Colby,
*/



(function () {

    Colby.afterDOMContentLoaded(
        afterDOMContentLoaded
    );



    /**
     * @return undefined
     */
    function afterDOMContentLoaded() {
        let elements = document.getElementsByClassName(
            "CBSignOutView"
        );

        for (let index = 0; index < elements.length; index += 1) {
            let element = elements.item(index);

            initialize(element);
        }
    }
    /* afterDOMContentLoaded() */



    /**
     * @return undefined
     */
    function initialize(
        element
    ) {
        let elements = CBUI.createElementTree(
            "CBUI_container1",
            "CBUI_button1"
        );

        element.appendChild(
            elements[0]
        );

        let buttonElement = elements[1];
        buttonElement.textContent = "Sign Out";

        buttonElement.addEventListener(
            "click",
            function () {
                signOutAndReload();
            }
        );
    }
    /* initialize() */



    /**
     * @return undefined
     */
    function signOutAndReload() {
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
    /* signOutAndReload() */

})();
