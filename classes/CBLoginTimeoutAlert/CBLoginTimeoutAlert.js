"use strict";
/* jshint strict: global */
/* global
    CBUserIsLoggedIn,
    Colby,
*/

var CBLoginTimeoutAlert = {

    /**
     * @return undefined
     */
    DOMContentDidLoad: function () {
        try {
            if (localStorage.getItem("CBUserIsLoggedIn") !== CBUserIsLoggedIn) {
                if (localStorage.getItem("CBUserIsLoggedIn")) {
                    var element = document.createElement("div");
                    element.textContent = (
                        "Your login has expired and you are currently " +
                        "logged out. Please login again if you need the " +
                        "privileges of a logged in user."
                    );

                    Colby.setPanelElement(element);
                    Colby.showPanel();
                }

                localStorage.setItem("CBUserIsLoggedIn", CBUserIsLoggedIn);
            }
        } catch (e) {
            // TODO: send a silent error to the server.
        }
    },
};

document.addEventListener("DOMContentLoaded", CBLoginTimeoutAlert.DOMContentDidLoad);
