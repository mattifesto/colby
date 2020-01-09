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
    function afterDOMContentLoaded() {
        let mainElement = document.getElementsByTagName("main").item(0);

        if (CBAdminPageForUserSettings_currentUserIsDeveloper) {
            let elements = CBUI.createElementTree(
                "CBUI_container1",
                "CBUI_button1"
            );

            mainElement.appendChild(
                elements[0]
            );


            let buttonElement = elements[1];
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

            elements = CBUI.createElementTree(
                "CBUI_container1",
                "CBUI_button1"
            );

            mainElement.appendChild(
                elements[0]
            );

            elements[1].textContent = "Inspect CBUser Model";
            elements[1].addEventListener(
                "click",
                function () {
                    inspectCBUserModel();
                }
            );
        }

        return;



        /* -- closures -- -- -- -- -- */



        /**
         * @return undefined
         */
        function inspectCBUserModel() {
            window.location = (
                "/admin/?c=CBModelInspector&ID=" +
                CBAdminPageForUserSettings_userCBID
            );
        }
        /* inspectCBUserModel() */

    }
    /* afterDOMContentLoaded() */
);
