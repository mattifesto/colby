"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* global
    CBErrorHandler,
    CBException,
    CBModel,
    CBUI,
    Colby,
*/



(function () {

    /* public API */

    window.CBFacebookAccountUserSettingsManager = {
        CBUserSettingsManager_createElement,
    };



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
        let targetUserCBID = CBModel.valueAsCBID(
            args,
            "targetUserCBID"
        );

        if (targetUserCBID === null) {
            throw CBException.withValueRelatedError(
                Error("The \"targetUserCBID\" argument is not valid."),
                args,
                "b406ab42194235888826fd72e0374ce653f17012"
            );
        }

        let element = CBUI.createElement(
            "CBFacebookAccountUserSettingsManager"
        );

        initializeElement(
            element,
            targetUserCBID
        );

        return element;
    }
    /* CBUserSettingsManager_createElement() */



    /**
     * @param Element element
     *
     * @return undefined
     */
    function initializeElement(
        element,
        targetUserCBID
    ) {
        let elements = CBUI.createElementTree(
            "CBUI_sectionContainer",
            "CBUI_section",
            "CBUI_sectionItem",
            "CBUI_container_topAndBottom CBUI_flexGrow",
            "label CBUI_textColor2"
        );

        element.appendChild(
            elements[0]
        );

        elements[4].textContent = "Facebook Account";

        let valueElement = CBUI.createElement(
            "value"
        );

        elements[3].appendChild(
            valueElement
        );

        valueElement.textContent = Colby.nonBreakingSpace;

        elements[2].appendChild(
            CBUI.createElement(
                "CBUI_navigationArrow"
            )
        );

        Colby.callAjaxFunction(
            "CBFacebookAccountUserSettingsManager",
            "fetchTargetUserData",
            {
                targetUserCBID,
            }
        ).then(
            function (targetUserData) {
                let accessWasDenied = CBModel.valueToBool(
                    targetUserData,
                    'accessWasDenied'
                );

                if (accessWasDenied) {
                    valueElement.textContent = "access denied";

                    valueElement.classList.add("CBUI_textColor2");

                    return;
                }

                if (targetUserData.facebookName === null) {
                    valueElement.textContent = "no account";

                    valueElement.classList.add("CBUI_textColor2");

                    return;
                }

                let facebookName = CBModel.valueToString(
                    targetUserData,
                    'facebookName'
                ).trim();

                if (facebookName === "") {
                    valueElement.textContent = "account has no name";

                    valueElement.classList.add("CBUI_textColor2");
                } else {
                    valueElement.textContent = facebookName;

                    valueElement.classList.remove("CBUI_textColor2");
                }
            }
        ).catch(
            function (error) {
                CBErrorHandler.displayAndReport(error);
            }
        );
    }
    /* initializeElement() */

})();
