/* global
    CBUI,
    CBUINavigationView,
    CBUserSettingsManager,
    Colby,

    CBAdminPageForUserSettings_userCBID,
    CBAdminPageForUserSettings_userSettingsManagerClassNames,
*/


(function () {
    "use strict";

    Colby.afterDOMContentLoaded(
        function () {
            afterDOMContentLoaded();
        }
    );

    function
    afterDOMContentLoaded() {
        if (
            document.getElementsByClassName(
                "CBAdminPageForUserSettings_notFound"
            ).length > 0
        ) {
            return;
        }

        let mainElement = document.getElementsByTagName("main").item(0);

        mainElement.appendChild(
            CBUINavigationView.create().element
        );

        let rootElement = CBUI.createElement();

        CBAdminPageForUserSettings_userSettingsManagerClassNames.forEach(
            function (
                className
            ) {
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

})();
