"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* global
    CBUserSettingsManager,
    Colby,

    CBAdminPageForUserSettings_userCBID,
    CBAdminPageForUserSettings_userSettingsManagerClassNames,
*/



Colby.afterDOMContentLoaded(
    function afterDOMContentLoaded() {
        let mainElement = document.getElementsByTagName("main").item(0);

        CBAdminPageForUserSettings_userSettingsManagerClassNames.forEach(
            function (className) {
                let element = CBUserSettingsManager.createElement(
                    {
                        className,
                        targetUserCBID: CBAdminPageForUserSettings_userCBID,
                    }
                );

                mainElement.appendChild(
                    element
                );
            }
        );
    }
    /* afterDOMContentLoaded() */
);
