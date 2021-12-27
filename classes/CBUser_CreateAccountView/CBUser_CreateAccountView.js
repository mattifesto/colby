/* global
    CB_UI_StringEditor,
    CBAjax,
    CBUI,
    CBUIButton,
    CBUIPanel,
    Colby,
*/


(function () {
    "use strict";

    Colby.afterDOMContentLoaded(
        afterDOMContentLoaded
    );



    /* -- closures -- -- -- -- -- */



    /**
     * @return undefined
     */
    function
    afterDOMContentLoaded(
    ) {
        let viewElements = document.getElementsByClassName(
            "CBUser_CreateAccountView"
        );

        for (
            let index = 0;
            index < viewElements.length;
            index += 1
        ) {
            let viewElement = viewElements.item(
                index
            );

            let destinationURL = viewElement.dataset.destinationURL;

            initializeViewElement(
                viewElement,
                destinationURL
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
    function
    createPotentialUserModel(
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
    function
    createSignUpForm(
        viewElement,
        resolve
    ) {
        let elements = CBUI.createElementTree(
            "CBUser_CreateAccountView_createPotentialUser"
        );

        let rootElement = elements[0];

        viewElement.appendChild(rootElement);

        let cbmessageElement = CBUI.cbmessageToElement(`

            --- CBUI_title1
            Create A New Account
            ---

            After you submit this form the website will send you an email
            containing a one time password to confirm that you have access to
            the email account. After you enter the one time password your
            account will be created and you will be signed in.

        `);

        rootElement.appendChild(
            cbmessageElement
        );

        /* form */

        let emailAddressEditor = CB_UI_StringEditor.create();

        emailAddressEditor.CB_UI_StringEditor_setTitle(
            "Email Address"
        );

        rootElement.append(
            emailAddressEditor.CB_UI_StringEditor_getElement()
        );


        let fullNameEditor = CB_UI_StringEditor.create();

        fullNameEditor.CB_UI_StringEditor_setTitle(
            "Full Name"
        );

        rootElement.append(
            fullNameEditor.CB_UI_StringEditor_getElement()
        );


        let password1Editor = CB_UI_StringEditor.create();

        password1Editor.CB_UI_StringEditor_setInputType(
            "CB_UI_StringEditor_inputType_password"
        );

        password1Editor.CB_UI_StringEditor_setTitle(
            "Password"
        );

        rootElement.append(
            password1Editor.CB_UI_StringEditor_getElement()
        );


        let password2Editor = CB_UI_StringEditor.create();

        password2Editor.CB_UI_StringEditor_setInputType(
            "CB_UI_StringEditor_inputType_password"
        );

        password2Editor.CB_UI_StringEditor_setTitle(
            "Re-enter Password"
        );

        rootElement.append(
            password2Editor.CB_UI_StringEditor_getElement()
        );


        let signUpButton = CBUIButton.create();

        signUpButton.CBUIButton_setTextContent(
            "Sign Up"
        );

        rootElement.append(
            signUpButton.CBUIButton_getElement()
        );

        signUpButton.CBUIButton_addClickEventListener(
            async function (
            ) {
                try {
                    signUpButton.CBUIButton_setIsDisabled(
                        true
                    );

                    let emailAddress = (
                        emailAddressEditor.CB_UI_StringEditor_getValue()
                    );

                    let fullName = (
                        fullNameEditor.CB_UI_StringEditor_getValue()
                    );

                    let password1 = (
                        password1Editor.CB_UI_StringEditor_getValue()
                    );

                    let password2 = (
                        password2Editor.CB_UI_StringEditor_getValue()
                    );

                    let response = await CBAjax.call(
                        "CBUser_PotentialUser",
                        "create",
                        {
                            emailAddress,
                            fullName,
                            password1,
                            password2,
                        }
                    );

                    if (
                        response.succeeded === true
                    ) {
                        resolve(
                            response.potentialUserCBID
                        );

                        /* don't re-enabled the sign up button */

                        return;
                    } else {
                        await CBUIPanel.displayCBMessage(
                            response.cbmessage
                        );

                        signUpButton.CBUIButton_setIsDisabled(
                            false
                        );
                    }
                } catch (
                    error
                ) {
                    CBUIPanel.displayError2(
                        error
                    );

                    signUpButton.CBUIButton_setIsDisabled(
                        false
                    );
                }
            }
        );
    }
    /* createSignUpForm() */



    /**
     * @param Element viewElement
     * @param CBID potentialUserCBID
     * @param function resolve
     *
     * @return undefined
     */
    function
    createVerificationForm(
        viewElement,
        potentialUserCBID,
        resolve
    ) {
        let elements = CBUI.createElementTree(
            "CBUser_CreateAccountView_verifyPotentialUser"
        );

        let rootElement = elements[0];

        viewElement.appendChild(
            rootElement
        );

        let cbmessageElement = CBUI.cbmessageToElement(`

            --- CBUI_title1
            Confirm Access to Email Address
            ---

            The website has sent an email containing a one time password to the
            email address you entered on the previous form. You should receive
            it momentarily. Enter the one time password into the form below,
            then press the Confirm button, then your account will be created and
            you will be signed in.

        `);

        rootElement.append(
            cbmessageElement
        );

        let oneTimePasswordEditor = CB_UI_StringEditor.create();

        oneTimePasswordEditor.CB_UI_StringEditor_setTitle(
            "One Time Password"
        );

        rootElement.append(
            oneTimePasswordEditor.CB_UI_StringEditor_getElement()
        );

        let confirmButton = CBUIButton.create();

        confirmButton.CBUIButton_setTextContent(
            "Confirm"
        );

        rootElement.append(
            confirmButton.CBUIButton_getElement()
        );

        confirmButton.CBUIButton_addClickEventListener(
            async function () {
                try {
                    confirmButton.CBUIButton_setIsDisabled(
                        true
                    );

                    let oneTimePassword = (
                        oneTimePasswordEditor.CB_UI_StringEditor_getValue()
                    );

                    let response = await CBAjax.call(
                        "CBUser_PotentialUser",
                        "verify",
                        {
                            potentialUserCBID,
                            oneTimePassword,
                        }
                    );

                    if (
                        response.succeeded === true
                    ) {
                        resolve();
                    } else {
                        await CBUIPanel.displayCBMessage(
                            response.cbmessage
                        );

                        confirmButton.CBUIButton_setIsDisabled(
                            false
                        );
                    }
                } catch (
                    error
                ) {
                    CBUIPanel.displayError2(
                        error
                    );

                    confirmButton.CBUIButton_setIsDisabled(
                        false
                    );
                }
            }
        );
    }
    /* createVerificationForm() */



    /**
     * @param Element viewElement
     * @param string destinationURL
     *
     * @return Promise -> undefined
     */
    async function
    initializeViewElement(
        viewElement,
        destinationURL
    ) {
        try {
            destinationURL = destinationURL || "/";

            let potentialUserCBID = await createPotentialUserModel(
                viewElement
            );

            viewElement.textContent = "";

            await verifyPotentialUser(
                viewElement,
                potentialUserCBID
            );

            window.location = destinationURL;
        } catch (error) {
            CBUIPanel.displayAndReportError(error);
        }
    }
    /* initializeViewElement() */



    /**
     * @param Element viewElement
     * @param CBID potentialUserCBID
     *
     * @return Promise -> undefined
     */
    function
    verifyPotentialUser(
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
