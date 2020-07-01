"use strict";
/* jshint strict: global */
/* jshint esversion: 8 */
/* global
    CBUI,
    CBUIPanel,
    CBUIStringEditor,
    Colby,
*/



(function () {

    Colby.afterDOMContentLoaded(afterDOMContentLoaded);



    /* -- closures -- -- -- -- -- */



    /**
     * @return undefined
     */
    function afterDOMContentLoaded() {
        let viewElements = document.getElementsByClassName(
            "CBUser_ResetPasswordView"
        );

        for (let index = 0; index < viewElements.length; index += 1) {
            let viewElement = viewElements.item(index);

            initializeViewElement(
                viewElement
            );
        }
    }
    /* afterDOMContentLoaded() */



    /**
     * @param string userEmailAddress
     *
     * @return Element
     *
     *      {
     *          promise_potentialPasswordCBID: Promise
     *      }
     */
    function createResetPasswordFormElement(
        userEmailAddress
    ) {
        let elements = CBUI.createElementTree(
            "CBUser_ResetPasswordView_resetPasswordForm",
            "CBUI_title1"
        );

        let rootElement = elements[0];

        elements[1].textContent = "Change/Reset Password";

        elements = CBUI.createElementTree(
            "CBUI_sectionContainer",
            "CBUI_section"
        );

        rootElement.appendChild(
            elements[0]
        );

        let sectionElement = elements[1];

        let emailAddressEditor = CBUIStringEditor.create();
        emailAddressEditor.title = "Email Address";
        emailAddressEditor.value = userEmailAddress;

        sectionElement.appendChild(
            emailAddressEditor.element
        );

        let password1Editor = CBUIStringEditor.create(
            {
                inputType: "password",
            }
        );

        password1Editor.title = "New Password";

        sectionElement.appendChild(
            password1Editor.element
        );

        let password2Editor = CBUIStringEditor.create(
            {
                inputType: "password",
            }
        );

        password2Editor.title = "Re-enter New Password";

        sectionElement.appendChild(
            password2Editor.element
        );

        elements = CBUI.createElementTree(
            "CBUI_container1",
            "CBUI_button1"
        );

        rootElement.appendChild(
            elements[0]
        );

        let buttonElement = elements[1];

        buttonElement.textContent = "Change/Reset Password";

        rootElement.promise_potentialPasswordCBID = new Promise(
            function (resolve) {
                buttonElement.addEventListener(
                    "click",
                    function () {
                        tryToCreatePotentialPasswordViaAjax(
                            emailAddressEditor.value,
                            password1Editor.value,
                            password2Editor.value,
                            resolve
                        );
                    }
                );
            }
        );

        return rootElement;
    }
    /* createResetPasswordFormElement() */



    /**
     * @param Element viewElement
     * @param CBID potentialPasswordCBID
     * @param function resolve
     *
     * @return undefined
     */
    function createVerificationForm(
        viewElement,
        potentialPasswordCBID,
        resolve
    ) {
        let elements = CBUI.createElementTree(
            "CBUser_ResetPasswordView_verifyPotentialPassword",
            "CBUI_title1"
        );

        let rootElement = elements[0];

        viewElement.appendChild(
            rootElement
        );

        elements[1].textContent = "Confirm";

        elements = CBUI.createElementTree(
            "CBUI_sectionContainer",
            "CBUI_section"
        );

        rootElement.appendChild(
            elements[0]
        );

        let sectionElement = elements[1];

        let oneTimePasswordEditor = CBUIStringEditor.create();
        oneTimePasswordEditor.title = "One Time Password";

        sectionElement.appendChild(
            oneTimePasswordEditor.element
        );

        elements = CBUI.createElementTree(
            "CBUI_container1",
            "CBUI_button1"
        );

        rootElement.appendChild(
            elements[0]
        );

        let buttonElement = elements[1];

        buttonElement.textContent = "Confirm";

        buttonElement.addEventListener(
            "click",
            closure_tryToVerifyPotentialPasswordViaAjax
        );



        /**
         * @return undefined
         */
        function closure_tryToVerifyPotentialPasswordViaAjax() {
            tryToVerifyPotentialPasswordViaAjax(
                potentialPasswordCBID,
                oneTimePasswordEditor.value,
                resolve
            );
        }
        /* closure_tryToVerifyPotentialPasswordViaAjax() */

    }
    /* createVerificationForm() */



    /**
     * @param Element viewElement
     *
     * @return Promise -> undefined
     */
    async function initializeViewElement(
        viewElement
    ) {
        try {
            let userEmailAddress = viewElement.dataset.userEmailAddress;

            let resetPasswordFormElement = createResetPasswordFormElement(
                userEmailAddress
            );

            viewElement.appendChild(
                resetPasswordFormElement
            );

            let potentialPasswordCBID = (
                await resetPasswordFormElement.promise_potentialPasswordCBID
            );

            viewElement.textContent = "";

            await verifyPotentialPassword(
                viewElement,
                potentialPasswordCBID
            );

            window.location = "/colby/user/";
        } catch (error) {
            CBUIPanel.displayAndReportError(error);
        }
    }
    /* initializeViewElement() */



    /**
     * @param string emailAddress
     * @param string password1
     * @param string password2
     * @param function resolve
     *
     *      The resolve() function will be called if a potential password is
     *      created.
     *
     * @return undefined
     */
    async function tryToCreatePotentialPasswordViaAjax(
        emailAddress,
        password1,
        password2,
        resolve
    ) {
        try {
            let response = await Colby.callAjaxFunction(
                "CBUser_PotentialPassword",
                "create",
                {
                    emailAddress,
                    password1,
                    password2,
                }
            );

            if (response.succeeded === true) {
                resolve(
                    response.potentialPasswordCBID
                );
            } else {
                CBUIPanel.displayCBMessage(
                    response.cbmessage
                );
            }
        } catch (error) {
            CBUIPanel.displayAndReportError(error);
        }
    }
    /* tryToCreatePotentialPasswordViaAjax() */



    /**
     * @param CBID potentialPasswordCBID
     * @param string oneTimePassword
     * @param function resolve
     *
     * @return Promise -> undefined
     */
    async function tryToVerifyPotentialPasswordViaAjax(
        potentialPasswordCBID,
        oneTimePassword,
        resolve
    ) {
        let response = await Colby.callAjaxFunction(
            "CBUser_PotentialPassword",
            "verify",
            {
                potentialPasswordCBID,
                oneTimePassword,
            }
        );

        if (response.succeeded === true) {
            resolve();
        } else {
            return CBUIPanel.displayCBMessage(
                response.cbmessage
            );
        }
    }
    /* tryToVerifyPotentialPasswordViaAjax() */



    /**
     * @param Element viewElement
     * @param CBID potentialPasswordCBID
     *
     * @return Promise -> undefined
     */
    function verifyPotentialPassword(
        viewElement,
        potentialPasswordCBID
    ) {
        return new Promise(
            function (resolve) {
                createVerificationForm(
                    viewElement,
                    potentialPasswordCBID,
                    resolve
                );
            }
        );
    }
    /* verifyPotentialPassword() */

})();
