"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* global
    CBErrorHandler,
    CBUI,
    CBUIBooleanSwitchPart,
    Colby,
*/



(function () {
    let targetElements = document.getElementsByClassName(
        "CBUserGroupMembershipToggleView"
    );

    for (let index = 0; index < targetElements.length; index += 1) {
        let targetElement = targetElements[index];

        initializeTargetElement(targetElement);
    }



    /* -- closures -- -- -- -- -- */



    function initializeTargetElement(
        targetElement
    ) {
        let userCBID = targetElement.dataset.userCBID;
        let userGroupClassName = targetElement.dataset.userGroupClassName;

        targetElement.classList.add("CBUI_sectionContainer");


        /* section */

        let sectionElement = CBUI.createElement("CBUI_section");

        targetElement.appendChild(sectionElement);


        /* section item */

        let sectionItemElement = CBUI.createElement("CBUI_sectionItem");

        sectionElement.appendChild(sectionItemElement);


        /* text container */

        let textContainerElement = CBUI.createElement(
            "CBUI_container_topAndBottom CBUI_flexGrow"
        );

        sectionItemElement.appendChild(textContainerElement);


        /* text */

        let textElement = CBUI.createElement();

        textContainerElement.appendChild(textElement);

        textElement.textContent = userGroupClassName;


        /* switch */

        let booleanSwitchPart = CBUIBooleanSwitchPart.create();


        /* get initial membership status */

        Colby.callAjaxFunction(
            "CBUserGroup",
            "userIsMemberOfUserGroup",
            {
                userCBID: userCBID,
                userGroupClassName: userGroupClassName,
            }
        ).then(
            function (isMember) {
                /* switch */

                sectionItemElement.appendChild(booleanSwitchPart.element);

                booleanSwitchPart.value = isMember;

                /**
                 * After setting initial membership statue, react to value
                 * changes made by the user.
                 */

                booleanSwitchPart.changed = function() {
                    let ajaxFunctionName = "removeUser";

                    if (booleanSwitchPart.value) {
                        ajaxFunctionName = "addUser";
                    }

                    Colby.callAjaxFunction(
                        "CBUserGroup",
                        ajaxFunctionName,
                        {
                            userCBID: userCBID,
                            userGroupClassName: userGroupClassName,
                        }
                    ).catch(
                        function (error) {
                            CBErrorHandler.displayAndReport(error);
                        }
                    );
                };
            }
        ).catch(
            function (error) {
                CBErrorHandler.displayAndReport(error);
            }
        );
    }
    /* initializeTargetElement() */

})();
