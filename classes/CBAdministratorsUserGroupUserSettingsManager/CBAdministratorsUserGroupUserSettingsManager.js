"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* global
    CBErrorHandler,
    CBException,
    CBModel,
    CBUI,
    CBUIBooleanSwitchPart,
    Colby,
*/



(function() {

    /* public API */

    window.CBAdministratorsUserGroupUserSettingsManager = {
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
            "CBAdministratorsUserGroupUserSettingsManager",
            "CBUI_sectionContainer",
            "CBUI_section",
            "CBUI_sectionItem",
            "CBUI_container_topAndBottom CBUI_flexGrow",
            "CBAdministratorsUserGroupUserSettingsManager_label"
        );

        let element = elements[0];

        sectionItemElement = elements[3];

        elements[5].textContent = "CBAdministratorsUserGroup";

        Colby.callAjaxFunction(
            "CBAdministratorsUserGroupUserSettingsManager",
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
            let ajaxFunctionName = "removeUser";

            if (switchPart.value) {
                ajaxFunctionName = "addUser";
            }

            Colby.callAjaxFunction(
                "CBUserGroup",
                ajaxFunctionName,
                {
                    userCBID: targetUserCBID,
                    userGroupClassName: 'CBAdministratorsUserGroup',
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
