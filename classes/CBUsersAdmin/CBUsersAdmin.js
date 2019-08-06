"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBUsersAdmin */
/* global
    CBUI,
    CBUINavigationArrowPart,
    CBUISectionItem4,
    CBUIStringsPart,
    CBUser,
    Colby,

    CBUsersAdmin_users,
*/

var CBUsersAdmin = {

    /**
     * @return undefined
     */
    init: function () {
        let mainElement = document.getElementsByTagName("main")[0];

        mainElement.appendChild(
            CBUI.createHeader(
                {
                    centerElement: CBUI.createHeaderTitle(
                        {
                            text: "Users",
                        }
                    ),
                }
            )
        );

        if (CBUsersAdmin_users.length > 0) {
            let sectionElement = CBUI.createSection();

            CBUsersAdmin_users.forEach(
                function (user) {
                    let sectionItem = CBUISectionItem4.create();

                    sectionItem.callback = function () {
                        window.location = CBUser.userIDToUserAdminPageURL(
                            user.hash
                        );
                    };

                    let stringsPart = CBUIStringsPart.create();
                    stringsPart.string1 = user.facebookName;

                    sectionItem.appendPart(stringsPart);
                    sectionItem.appendPart(CBUINavigationArrowPart.create());
                    sectionElement.appendChild(sectionItem.element);
                }
            );

            mainElement.appendChild(CBUI.createHalfSpace());
            mainElement.appendChild(sectionElement);
            mainElement.appendChild(CBUI.createHalfSpace());
        }
    }
    /* init() */
};

Colby.afterDOMContentLoaded(CBUsersAdmin.init);
