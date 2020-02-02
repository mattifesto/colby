"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* global
    CBUI,
    CBUINavigationView,
    CBUserSettingsManager,
    Colby,

    CBAdminPageForUserSettings_userCBID,
    CBAdminPageForUserSettings_userSettingsManagerClassNames,
*/



Colby.afterDOMContentLoaded(
    function afterDOMContentLoaded() {
        let mainElement = document.getElementsByTagName("main").item(0);

        mainElement.appendChild(
            CBUINavigationView.create().element
        );

        let rootElement = CBUI.createElement();

        CBAdminPageForUserSettings_userSettingsManagerClassNames.forEach(
            function (className) {
                let element = CBUserSettingsManager.createElement(
                    {
                        className,
                        targetUserCBID: CBAdminPageForUserSettings_userCBID,
                    }
                );

                rootElement.appendChild(
                    element
                );
            }
        );

        CBUINavigationView.navigate(
            {
                element: rootElement,
                title: "User",
            }
        );
    }
    /* afterDOMContentLoaded() */
);
