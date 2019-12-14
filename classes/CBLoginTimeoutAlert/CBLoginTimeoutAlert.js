"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* global
    CBUIPanel,
    Colby,

    CBLoginTimeoutAlert_userIsLoggedIn,
*/

Colby.afterDOMContentLoaded(
    function () {
        let userWasLoggedIn = false;

        /**
         * If localStorage is disabled in the browser an error will be thrown
         * which also means they are not logged in and cannot be.
         */

        try {
            userWasLoggedIn = (
                localStorage.getItem(
                    "CBLoginTimeoutAlert_userWasLogggedIn"
                ) === "yes"
            );
        } catch (error) {
            return;
        }

        if (userWasLoggedIn) {
            if (!CBLoginTimeoutAlert_userIsLoggedIn) {
                CBUIPanel.displayText(
                    "Your login has expired and you are currently " +
                    "logged out. Please login again if you need the " +
                    "privileges of a logged in user."
                );
            }
        }

        localStorage.setItem(
            "CBLoginTimeoutAlert_userWasLogggedIn",
            CBLoginTimeoutAlert_userIsLoggedIn ? "yes" : "no"
        );
    }
);
