"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* global
    CBException,
    CBModel,
    CBUI,
    CBUIPanel,
    CBUIThumbnailPart,
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
            "CBUI_sectionItem"
        );

        element.appendChild(
            elements[0]
        );

        let sectionItemElement = elements[2];


        /* thumbnail */

        let thumbnailPart = CBUIThumbnailPart.create();

        sectionItemElement.appendChild(
            thumbnailPart.element
        );

        /* text */

        elements = CBUI.createElementTree(
            "CBUI_container_topAndBottom CBUI_flexGrow",
            "label CBUI_textColor2"
        );

        sectionItemElement.appendChild(
            elements[0]
        );

        elements[1].textContent = "Facebook Account";

        let valueElement = CBUI.createElement(
            "value"
        );

        elements[0].appendChild(
            valueElement
        );

        valueElement.textContent = Colby.nonBreakingSpace;


        /* navigation arrow */

        sectionItemElement.appendChild(
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
                    "accessWasDenied"
                );

                if (accessWasDenied) {
                    valueElement.textContent = "access denied";

                    valueElement.classList.add("CBUI_textColor2");

                    return;
                }

                if (targetUserData.facebookUserFullName === null) {
                    valueElement.textContent = "no account";

                    valueElement.classList.add("CBUI_textColor2");

                    return;
                }


                /* image */

                thumbnailPart.src = CBModel.valueToString(
                    targetUserData,
                    "facebookUserImageURL"
                );


                /* full name */

                let facebookUserFullName = CBModel.valueToString(
                    targetUserData,
                    "facebookUserFullName"
                ).trim();

                if (facebookUserFullName === "") {
                    valueElement.textContent = "account has no name";

                    valueElement.classList.add("CBUI_textColor2");
                } else {
                    valueElement.textContent = facebookUserFullName;

                    valueElement.classList.remove("CBUI_textColor2");
                }
            }
        ).catch(
            function (error) {
                CBUIPanel.displayAndReportError(error);
            }
        );
    }
    /* initializeElement() */

})();
