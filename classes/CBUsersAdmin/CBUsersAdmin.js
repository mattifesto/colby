"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBUsersAdmin */
/* global
    CBUI,
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
            let sectionContainerElement = CBUI.createElement(
                "CBUI_sectionContainer"
            );

            mainElement.appendChild(
                sectionContainerElement
            );

            let sectionElement = CBUI.createElement(
                "CBUI_section"
            );

            sectionContainerElement.appendChild(
                sectionElement
            );

            CBUsersAdmin_users.forEach(
                function (user) {
                    let sectionItemElement = CBUI.createElement(
                        "CBUI_sectionItem"
                    );

                    sectionElement.appendChild(
                        sectionItemElement
                    );

                    sectionItemElement.addEventListener(
                        "click",
                        function () {
                            window.location = CBUser.userIDToUserAdminPageURL(
                                user.hash
                            );
                        }
                    );

                    let textContainerElement = CBUI.createElement(
                        "CBUI_container_topAndBottom CBUI_flexGrow"
                    );

                    sectionItemElement.appendChild(
                        textContainerElement
                    );

                    let titleElement = CBUI.createElement();

                    textContainerElement.appendChild(
                        titleElement
                    );

                    titleElement.textContent = (
                        user.facebookName ||
                        "(no full name)"
                    );

                    let descriptionElement = CBUI.createElement(
                        "CBUI_ellipsis CBUI_textSize_small CBUI_textColor2"
                    );

                    descriptionElement.textContent = (
                        user.email ||
                        "(no email address)"
                    );

                    textContainerElement.appendChild(
                        descriptionElement
                    );

                    let navigationArrowElement = CBUI.createElement(
                        "CBUI_navigationArrow"
                    );

                    sectionItemElement.appendChild(
                        navigationArrowElement
                    );
                }
            );
        }
    }
    /* init() */
};

Colby.afterDOMContentLoaded(CBUsersAdmin.init);
