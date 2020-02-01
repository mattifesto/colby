"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* global
    CBErrorHandler,
    CBException,
    CBModel,
    CBUI,
    CBUINavigationView,
    CBUIStringEditor,
    Colby,
*/



(function () {

    /* public API */

    window.CBEmailAddressUserSettingsManager = {
        CBUserSettingsManager_createElement,
    };



    let emailAddressElement;
    let sectionItemElement;



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
                "6995d93f83c229797ff475860a76fb1f338836c3"
            );
        }

        let elements = CBUI.createElementTree(
            "CBEmailAddressUserSettingsManager",
            "CBUI_sectionContainer",
            "CBUI_section",
            "CBUI_sectionItem",
            "CBUI_container_topAndBottom CBUI_flexGrow",
            "CBEmailAddressUserSettingsManager_label CBUI_textColor2"
        );

        let element = elements[0];

        sectionItemElement = elements[3];

        elements[5].textContent = "Email Address";

        emailAddressElement = CBUI.createElement(
            "CBEmailAddressUserSettingsManager_value"
        );

        let textContainerElement = elements[4];

        textContainerElement.appendChild(
            emailAddressElement
        );

        Colby.callAjaxFunction(
            "CBEmailAddressUserSettingsManager",
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
     * @return undefined
     */
    function changeEmailAddress() {
        /* current email address */

        let elements = CBUI.createElementTree(
            "CBEmailAddressUserSettingsManager_changeEmailAddress",
            "CBUI_sectionContainer",
            "CBUI_section",
            "CBUI_container_topAndBottom",
            "CBUI_textColor2"
        );

        let rootElement = elements[0];

        elements[4].textContent = "Current Email Address";

        let currentEmailAddressElement = CBUI.createElement();
        currentEmailAddressElement.textContent = emailAddressElement.textContent;

        elements[3].appendChild(
            currentEmailAddressElement
        );


        /* new email address */

        elements = CBUI.createElementTree(
            "CBUI_sectionContainer",
            "CBUI_section"
        );

        rootElement.appendChild(
            elements[0]
        );

        let sectionElement = elements[1];

        let newEmailEditor = CBUIStringEditor.create();
        newEmailEditor.title = "New Email";

        sectionElement.appendChild(
            newEmailEditor.element
        );

        let newEmailEditor2 = CBUIStringEditor.create();
        newEmailEditor2.title = "Re-enter New Email";

        sectionElement.appendChild(
            newEmailEditor2.element
        );

        let passwordEditor = CBUIStringEditor.create(
            {
                inputType: "password",
            }
        );

        passwordEditor.title = "Password";

        sectionElement.appendChild(
            passwordEditor.element
        );

        elements = CBUI.createElementTree(
            "CBUI_container1",
            "CBUI_button1"
        );

        rootElement.appendChild(
            elements[0]
        );

        let buttonElement = elements[1];

        buttonElement.textContent = "Change Email Address";

        buttonElement.addEventListener(
            "click",
            function () {
                // TODO
            }
        );

        CBUINavigationView.navigate(
            {
                element: rootElement,
                title: "Change Email Address",
            }
        );
    }
    /* changeEmailAddress() */



    /**
     * @param object targetUserData
     */
    function initialize(
        targetUserData
    ) {
        emailAddressElement.textContent = targetUserData.targetUserEmailAddress;

        if (targetUserData.currentUserCanChange) {
            let navigationArrowElement = CBUI.createElement(
                "CBUI_navigationArrow"
            );

            sectionItemElement.appendChild(
                navigationArrowElement
            );

            sectionItemElement.addEventListener(
                "click",
                changeEmailAddress
            );
        }
    }
    /* initialize() */

})();
