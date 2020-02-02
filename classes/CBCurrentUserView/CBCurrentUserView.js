"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* global
    CBUI,
    CBUINavigationView,
    CBUserSettingsManager,
    Colby,

    CBCurrentUserView_userCBID,
    CBCurrentUserView_userSettingsManagerClassNames,
*/



(function () {

    Colby.afterDOMContentLoaded(afterDOMContentLoaded);



    /**
     * CBCurrentUserView is a view that is meant to be displayed only once per
     * page and should be the only view inside the main element of that page.
     * Any other use will produce unpredictable results.
     *
     * @return undefined
     */
    function afterDOMContentLoaded() {
        if (CBCurrentUserView_userCBID === null) {
            return;
        }

        let viewElements = document.getElementsByClassName(
            "CBCurrentUserView"
        );

        if (viewElements.length < 1) {
            return;
        }

        let viewElement = viewElements[0];

        viewElement.appendChild(
            CBUINavigationView.create().element
        );

        let rootNavigationItemElement = CBUI.createElement(
            "CBCurrentUserView_rootNavigationItemElement"
        );

        CBCurrentUserView_userSettingsManagerClassNames.forEach(
            function (className) {
                let element = CBUserSettingsManager.createElement(
                    {
                        className,
                        targetUserCBID: CBCurrentUserView_userCBID,
                    }
                );

                rootNavigationItemElement.appendChild(
                    element
                );
            }
        );

        CBUINavigationView.navigate(
            {
                element: rootNavigationItemElement,
                title: "User",
            }
        );
    }
    /* afterDOMContentLoaded() */

})();
