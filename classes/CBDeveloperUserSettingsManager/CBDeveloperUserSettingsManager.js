"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* global
    CBAjax,
    CBException,
    CBModel,
    CBUI,
    CBUIPanel,
*/


(function () {

    /* public API */

    window.CBDeveloperUserSettingsManager = {
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
                "0f5e7279feac999ea69f1360e80a0fe4dc7e4b87"
            );
        }

        let elements = CBUI.createElementTree(
            "CBDeveloperUserSettingsManager",
            "CBUI_container1",
            "CBUI_button1"
        );

        let element = elements[0];


        /* login as this user */

        let buttonElement = elements[2];
        buttonElement.textContent = "Login as this User";

        buttonElement.addEventListener(
            "click",
            function () {
                CBAjax.call(
                    "CBDeveloperUserSettingsManager",
                    "switchToUser",
                    {
                        userCBID: targetUserCBID,
                    }
                ).then(
                    function () {
                        window.location.reload(true);
                    }
                ).catch(
                    function (error) {
                        CBUIPanel.displayAndReportError(error);
                    }
                );
            }
        );


        /* inspect user model */

        elements = CBUI.createElementTree(
            "CBUI_container1",
            "CBUI_button1"
        );

        element.appendChild(
            elements[0]
        );

        elements[1].textContent = "Inspect CBUser Model";
        elements[1].addEventListener(
            "click",
            function () {
                inspectCBUserModel(
                    targetUserCBID
                );
            }
        );

        return element;
    }
    /* CBUserSettingsManager_createElement() */



    /**
     * @param CBID targetUserCBID
     *
     * @return undefined
     */
    function inspectCBUserModel(
        targetUserCBID
    ) {
        window.location = (
            "/admin/?c=CBModelInspector&ID=" +
            targetUserCBID
        );
    }
    /* inspectCBUserModel() */

})();
