"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* global
    CBErrorHandler,
    CBException,
    CBModel,
    CBUI,
    CBUIBooleanSwitchPart,
    CBUIPanel,
    Colby,
*/



(function() {

    /* public API */

    window.CBDevelopersUserGroupUserSettingsManager = {
        CBUserSettingsManager_createElement,
    };



    let sectionItemElement;
    let targetUserCBID;



    /**
     * @param object args
     *
     *      {
     *          targetUserCBID: CBID
     *      }
     *
     * @return Element
     */
    function CBUserSettingsManager_createElement(
        args
    ) {
        targetUserCBID = CBModel.valueAsCBID(
            args,
            "targetUserCBID"
        );

        if (targetUserCBID === null) {
            throw CBException.withValueRelatedError(
                Error("The \"targetUserCBID\" argument is not valid."),
                args,
                "5413e99038b0474eb8df8375ddd68b975921ab6e"
            );
        }

        let elements = CBUI.createElementTree(
            "CBDevelopersUserGroupUserSettingsManager",
            "CBUI_sectionContainer",
            "CBUI_section",
            "CBUI_sectionItem",
            "CBUI_container_topAndBottom CBUI_flexGrow",
            "CBDevelopersUserGroupUserSettingsManager_label"
        );

        let element = elements[0];

        sectionItemElement = elements[3];

        elements[5].textContent = "CBDevelopersUserGroup";

        Colby.callAjaxFunction(
            "CBDevelopersUserGroupUserSettingsManager",
            "fetchTargetUserData",
            {
                targetUserCBID,
            }
        ).then(
            function (targetUserData) {
                initialize(targetUserData);
            }
        ).catch(
            function (error) {
                CBErrorHandler.displayAndReport(error);
            }
        );

        return element;
    }
    /* CBUserSettingsManager_createElement() */



    /**
     * @param object targetUserData
     *
     * @return undefined
     */
    function initialize(targetUserData) {
        if (targetUserData.accessWasDenied) {
            return;
        }

        let switchPart = CBUIBooleanSwitchPart.create();

        sectionItemElement.appendChild(
            switchPart.element
        );

        switchPart.value = targetUserData.targetUserIsMemberOfUserGroup;

        switchPart.changed = function () {
            let ajaxFunctionName;
            let confirm;

            if (switchPart.value) {
                ajaxFunctionName = "addUser";

                confirm = Promise.resolve(true);
            } else {
                ajaxFunctionName = "removeUser";

                confirm = CBUIPanel.confirmText(
                    "Are you sure you want to remove this user " +
                    "from the CBDevelopersUserGroup?"
                );
            }

            confirm.then(
                function (wasConfirmed) {
                    if (wasConfirmed) {
                        return Colby.callAjaxFunction(
                            "CBUserGroup",
                            ajaxFunctionName,
                            {
                                userCBID: targetUserCBID,
                                userGroupClassName: 'CBDevelopersUserGroup',
                            }
                        );
                    } else {
                        switchPart.value = true;
                    }
                }
            ).catch(
                function (error) {
                    CBErrorHandler.displayAndReport(error);
                }
            );
        };
    }
    /* initialize() */

})();
