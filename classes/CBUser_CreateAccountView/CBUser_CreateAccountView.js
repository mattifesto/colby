"use strict";
/* jshint strict: global */
/* jshint esversion: 8 */
/* global
    CBErrorHandler,
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
            "CBUser_CreateAccountView"
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
     * @param Element viewElement
     *
     * @return Promise -> CBID
     *
     *      This promise returns the CBID of a CBUser_PotentialUser model.
     */
    function createPotentialUserModel(
        viewElement
    ) {
        return new Promise(
            function (resolve) {
                createSignUpForm(
                    viewElement,
                    resolve
                );
            }
        );
    }
    /* createPotentialUserModel() */



    /**
     * @param Element viewElement
     * @param function resolve
     *
     * @return undefined
     */
    function createSignUpForm(
        viewElement,
        resolve
    ) {
        let elements = CBUI.createElementTree(
            "CBUser_CreateAccountView_createPotentialUser",
            "CBUI_title1"
        );

        let rootElement = elements[0];

        viewElement.appendChild(rootElement);

        elements[1].textContent = "Sign Up";

        /* form */

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

        sectionElement.appendChild(
            emailAddressEditor.element
        );

        let fullNameEditor = CBUIStringEditor.create();
        fullNameEditor.title = "Full Name";

        sectionElement.appendChild(
            fullNameEditor.element
        );

        let password1Editor = CBUIStringEditor.create(
            {
                inputType: "password",
            }
        );

        password1Editor.title = "Password";

        sectionElement.appendChild(
            password1Editor.element
        );

        let password2Editor = CBUIStringEditor.create(
            {
                inputType: "password",
            }
        );

        password2Editor.title = "Re-enter Password";

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

        buttonElement.textContent = "Sign Up";

        buttonElement.addEventListener(
            "click",
            closure_tryToCreatePotentialUserViaAjax
        );



        /**
         * @return undefined
         */
        function closure_tryToCreatePotentialUserViaAjax() {
            tryToCreatePotentialUserViaAjax(
                emailAddressEditor.value,
                fullNameEditor.value,
                password1Editor.value,
                password2Editor.value,
                resolve
            );
        }
        /* closure_tryToCreatePotentialUserViaAjax() */

    }
    /* createSignUpForm() */



    /**
     * @param Element viewElement
     * @param CBID potentialUserCBID
     * @param function resolve
     *
     * @return undefined
     */
    function createVerificationForm(
        viewElement,
        potentialUserCBID,
        resolve
    ) {
        let elements = CBUI.createElementTree(
            "CBUser_CreateAccountView_verifyPotentialUser",
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
            closure_tryToVerifyPotentialUserViaAjax
        );



        /**
         * @return undefined
         */
        function closure_tryToVerifyPotentialUserViaAjax() {
            tryToVerifyPotentialUserViaAjax(
                potentialUserCBID,
                oneTimePasswordEditor.value,
                resolve
            );
        }
        /* closure_tryToVerifyPotentialUserViaAjax() */

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
            let potentialUserCBID = await createPotentialUserModel(
                viewElement
            );

            viewElement.textContent = "";

            await verifyPotentialUser(
                viewElement,
                potentialUserCBID
            );

            window.location = "/colby/user/";
        } catch (error) {
            CBErrorHandler.displayAndReport(error);
        }
    }
    /* initializeViewElement() */



    /**
     * @param string emailAddress
     * @param string fullName
     * @param string password1
     * @param string password2
     * @param function resolve
     *
     *      When a CBUser_PotentialUser model is created, this function will be
     *      called with the potentialUserCBID as the argument.
     *
     * @return Promise -> CBID
     */
    async function tryToCreatePotentialUserViaAjax(
        emailAddress,
        fullName,
        password1,
        password2,
        resolve
    ) {
        let response = await Colby.callAjaxFunction(
            "CBUser_PotentialUser",
            "create",
            {
                emailAddress,
                fullName,
                password1,
                password2,
            }
        );

        if (response.succeeded === true) {
            resolve(
                response.potentialUserCBID
            );
        } else {
            return CBUIPanel.displayCBMessage(
                response.cbmessage
            );
        }
    }
    /* tryToCreatePotentialUserViaAjax() */



    /**
     * @param CBID potentialUserCBID
     * @param string oneTimePassword
     * @param function resolve
     *
     * @return Promise -> undefined
     */
    async function tryToVerifyPotentialUserViaAjax(
        potentialUserCBID,
        oneTimePassword,
        resolve
    ) {
        let response = await Colby.callAjaxFunction(
            "CBUser_PotentialUser",
            "verify",
            {
                potentialUserCBID,
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
    /* tryToVerifyPotentialUserViaAjax() */



    /**
     * @param Element viewElement
     * @param CBID potentialUserCBID
     *
     * @return Promise -> undefined
     */
    function verifyPotentialUser(
        viewElement,
        potentialUserCBID
    ) {
        return new Promise(
            function (resolve) {
                createVerificationForm(
                    viewElement,
                    potentialUserCBID,
                    resolve
                );
            }
        );
    }
    /* verifyPotentialUser() */

})();
