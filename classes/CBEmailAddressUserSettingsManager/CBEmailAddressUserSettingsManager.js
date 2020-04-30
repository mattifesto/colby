"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* global
    CBException,
    CBModel,
    CBUI,
    CBUINavigationView,
    CBUIPanel,
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
                CBUIPanel.displayAndReportError(error);
            }
        );

        return element;
    }
    /* CBUserSettingsManager_createElement() */



    /**
     * @return undefined
     */
    function createAddEmailElement() {
        let element = CBUI.createElement(
            "CBEmailAddressUserSettingsManager_emailEditor"
        );

        CBUINavigationView.navigate(
            {
                element: element,
                title: "Add Email Address",
            }
        );

        element.appendChild(
            CBUI.cbmessageToElement(
                `
                    (Add Email Address (b))((br))
                    You can add an email address and password to your
                    account below.
                `
            )
        );

        let elements = CBUI.createElementTree(
            "CBUI_sectionContainer",
            "CBUI_section"
        );

        element.appendChild(
            elements[0]
        );

        let emailEditor = CBUIStringEditor.create();
        emailEditor.title = "Email Address";

        elements[1].appendChild(
            emailEditor.element
        );

        let emailEditor2 = CBUIStringEditor.create();
        emailEditor2.title = "Re-enter Email Address";

        elements[1].appendChild(
            emailEditor2.element
        );

        let passwordEditor = CBUIStringEditor.create(
            {
                inputType: "password",
            }
        );

        passwordEditor.title = "Password";

        elements[1].appendChild(
            passwordEditor.element
        );

        let passwordEditor2 = CBUIStringEditor.create(
            {
                inputType: "password",
            }
        );

        passwordEditor2.title = "Re-enter Password";

        elements[1].appendChild(
            passwordEditor2.element
        );

        elements = CBUI.createElementTree(
            "CBUI_container1",
            "CBUI_button1"
        );

        element.appendChild(
            elements[0]
        );

        elements[1].textContent = "Add Email Address";

        elements[1].addEventListener(
            "click",
            function () {
                addEmailAddress();
            }
        );



        /* -- closures -- -- -- -- -- */



        /**
         * @return undefined
         */
        function addEmailAddress() {
            Colby.callAjaxFunction(
                "CBUser",
                "addEmailAddress",
                {
                    email: emailEditor.value.trim(),
                    email2: emailEditor2.value.trim(),
                    password: passwordEditor.value,
                    password2: passwordEditor2.value,
                }
            ).then(
                function (response) {
                    if (response.succeeded === true) {
                        window.location.reload();
                    } else {
                        CBUIPanel.displayCBMessage(
                            response.cbmessage
                        );
                    }
                }
            ).catch(
                function (error) {
                    CBUIPanel.displayAndReportError(error);
                }
            );
        }
        /* addEmailAddress() */

    }
    /* createAddEmailElement() */



    /**
     * @return undefined
     */
    function createEmailEditorElement() {
        let element = CBUI.createElement(
            "CBEmailAddressUserSettingsManager_emailEditor"
        );

        CBUINavigationView.navigate(
            {
                element: element,
                title: "Change Email Address",
            }
        );

        element.appendChild(
            CBUI.cbmessageToElement(
                `
                    (Change Email Address (b))((br))
                    You can change your email address below.
                `
            )
        );

        let elements = CBUI.createElementTree(
            "CBUI_sectionContainer",
            "CBUI_section",
            "CBUI_container_topAndBottom",
            "CBUI_textColor2"
        );

        element.appendChild(
            elements[0]
        );

        elements[3].textContent = "Current Email";

        let emailElement = CBUI.createElement("");

        elements[2].appendChild(
            emailElement
        );

        if (emailAddressElement.textContent === "") {
            emailElement.textContent = Colby.nonBreakingSpace;
        } else {
            emailElement.textContent = emailAddressElement.textContent;
        }

        let newEmailEditor = CBUIStringEditor.create();
        newEmailEditor.title = "New Email";

        elements[1].appendChild(
            newEmailEditor.element
        );

        let newEmailEditor2 = CBUIStringEditor.create();
        newEmailEditor2.title = "Re-enter New Email";

        elements[1].appendChild(
            newEmailEditor2.element
        );

        let passwordEditor = CBUIStringEditor.create(
            {
                inputType: "password",
            }
        );

        passwordEditor.title = "Password";

        elements[1].appendChild(
            passwordEditor.element
        );

        elements = CBUI.createElementTree(
            "CBUI_container1",
            "CBUI_button1"
        );

        element.appendChild(
            elements[0]
        );

        elements[1].textContent = "Change Email Address";

        elements[1].addEventListener(
            "click",
            function () {
                changeEmailAddress();
            }
        );



        /* -- closures -- -- -- -- -- */



        /**
         * @return undefined
         */
        function changeEmailAddress() {
            Colby.callAjaxFunction(
                "CBUser",
                "changeEmailAddress",
                {
                    email: newEmailEditor.value.trim(),
                    email2: newEmailEditor2.value.trim(),
                    password: passwordEditor.value,
                }
            ).then(
                function (response) {
                    if (response.succeeded === true) {
                        window.location.reload();
                    } else {
                        CBUIPanel.displayCBMessage(
                            response.cbmessage
                        );
                    }
                }
            ).catch(
                function (error) {
                    CBUIPanel.displayAndReportError(error);
                }
            );
        }
    }
    /* createEmailEditorElement() */



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
                function () {
                    if (emailAddressElement.textContent === "") {
                        createAddEmailElement();
                    } else {
                        createEmailEditorElement();
                    }
                }
            );
        }
    }
    /* initialize() */

})();
