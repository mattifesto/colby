"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* global
    CBErrorHandler,
    CBUI,
    Colby,

    CBAdminPageForUserSettings_currentUserIsDeveloper,
    CBAdminPageForUserSettings_userCBID,
*/



Colby.afterDOMContentLoaded(
    function () {
        let mainElement = document.getElementsByTagName("main").item(0);

        if (CBAdminPageForUserSettings_currentUserIsDeveloper) {
            let containerElement = CBUI.createElement(
                "CBUI_container1"
            );

            mainElement.appendChild(
                containerElement
            );


            let buttonElement = CBUI.createElement(
                "CBUI_button1"
            );

            containerElement.appendChild(
                buttonElement
            );

            buttonElement.textContent = "Login as this User";

            buttonElement.addEventListener(
                "click",
                function () {
                    Colby.callAjaxFunction(
                        "CBUser",
                        "switchToUser",
                        {
                            userCBID: CBAdminPageForUserSettings_userCBID,
                        }
                    ).then(
                        function () {
                            window.location.reload(true);
                        }
                    ).catch(
                        function (error) {
                            CBErrorHandler.displayAndReport(error);
                        }
                    );
                }
            );
        }
    }
);
