"use strict"; /* jshint strict: global */
/* global
    CBUserIsLoggedIn,
    Colby
*/

var CBLoginTimeoutAlert = {

    /**
     * @return undefined
     */
    DOMContentDidLoad: function () {
        if (!Colby.localStorageIsSupported()) {
            return;
        }

        if (localStorage.CBUserIsLoggedIn !== CBUserIsLoggedIn) {
            if (localStorage.CBUserIsLoggedIn) {
                var element = document.createElement("div");
                element.textContent = "Your login has expired and you are currently logged out. Please login again if you need the privileges of a logged in user.";

                Colby.setPanelElement(element);
                Colby.showPanel();

                localStorage.CBUserIsLoggedIn = CBUserIsLoggedIn;
            } else {
                localStorage.CBUserIsLoggedIn = CBUserIsLoggedIn;
            }
        }
    },
};

document.addEventListener("DOMContentLoaded", CBLoginTimeoutAlert.DOMContentDidLoad);
