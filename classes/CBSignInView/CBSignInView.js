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


    /**
     * @return undefined
     */
    function afterDOMContentLoaded() {
        let viewElements = document.getElementsByClassName("CBSignInView");

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
     * @param function resolve
     *
     *      This function will call resolve() when a user is successfully signed
     *      in.
     *
     * @return undefined
     */
    function createUserInterface(
        viewElement,
        resolve
    ) {
        let elements = CBUI.createElementTree(
            ["CBSignInView_signIn", "form"],
            "CBUI_title1"
        );

        let formElement = elements[0];

        viewElement.appendChild(
            formElement
        );

        formElement.onsubmit = function (event) {
            event.preventDefault();

            closure_tryToSignInViaAjax();
        };

        elements[1].textContent = "Sign In";

        elements = CBUI.createElementTree(
            "CBUI_sectionContainer",
            "CBUI_section"
        );

        formElement.appendChild(
            elements[0]
        );

        let sectionElement = elements[1];

        let emailAddressEditor = CBUIStringEditor.create();
        emailAddressEditor.title = "email";

        sectionElement.appendChild(
            emailAddressEditor.element
        );

        let passwordEditor = CBUIStringEditor.create(
            {
                inputType: "password",
            }
        );

        passwordEditor.title = "password";

        sectionElement.appendChild(
            passwordEditor.element
        );

        /* sign in button */

        elements = CBUI.createElementTree(
            "CBUI_container1",
            "CBUI_button1"
        );

        formElement.appendChild(
            elements[0]
        );

        let signInButtonElement = elements[1];

        signInButtonElement.textContent = "Sign In";
        signInButtonElement.tabIndex = 0;

        signInButtonElement.addEventListener(
            "keydown",
            function (event) {
                if (event.key === "Enter") {
                    signInButtonElement.blur();

                    closure_tryToSignInViaAjax();
                }
            }
        );

        signInButtonElement.addEventListener(
            "click",
            closure_tryToSignInViaAjax
        );

        viewElement.appendChild(
            createResetPasswordElement()
        );

        viewElement.appendChild(
            createSignUpElement()
        );



        /**
         * @return undefined
         */
        function closure_tryToSignInViaAjax() {
            tryToSignInViaAjax(
                emailAddressEditor.value,
                passwordEditor.value,
                resolve,
            );
        }
        /* closure_tryToSignInViaAjax() */

    }
    /* createUserInterface() */



    /**
     * Creates a button that links to the sign up page.
     *
     * @return Element
     */
    function createResetPasswordElement() {
        let elements = CBUI.createElementTree(
            "CBSignInView_signUp",
            "CBUI_title1"
        );

        let element = elements[0];

        elements[1].textContent = "Have you forgotten your password?";

        elements = CBUI.createElementTree(
            "CBUI_sectionContainer",
            "CBUI_section",
            ["CBUI_action", "a"]
        );

        element.appendChild(
            elements[0]
        );

        elements[2].textContent = "Reset Password >";
        elements[2].href = "/colby/user/reset-password/";

        return element;
    }
    /* createResetPasswordElement() */



    /**
     * Creates a button that links to the sign up page.
     *
     * @return Element
     */
    function createSignUpElement() {
        let elements = CBUI.createElementTree(
            "CBSignInView_signUp",
            "CBUI_title1"
        );

        let element = elements[0];

        elements[1].textContent = "Do you need to create an account?";

        elements = CBUI.createElementTree(
            "CBUI_sectionContainer",
            "CBUI_section",
            ["CBUI_action", "a"]
        );

        element.appendChild(
            elements[0]
        );

        elements[2].textContent = "Sign Up / Create Account >";
        elements[2].href = "/colby/user/create-account/";

        return element;
    }
    /* createSignUpElement() */



    /**
     * @param Element viewElement
     */
    async function initializeViewElement(
        viewElement
    ) {
        try {
            await signInUser(viewElement);

            window.location.reload();
        } catch (error) {
            CBErrorHandler.displayAndReport(error);
        }
    }
    /* initializeViewElement() */



    /**
     * This is the function that using a view element will present user
     * interface that will allow a user to sign in. When a use successfully
     * signs in that promise will resolve.
     *
     * @param Element viewElement
     *
     * @return Promise -> undefined
     */
    async function signInUser(
        viewElement
    ) {
        return new Promise(
            function (resolve) {
                createUserInterface(
                    viewElement,
                    resolve
                );
            }
        );
    }
    /* signInUser() */



    /**
     * @param string emailAddress
     * @param string password
     * @param function resolve
     *
     *      This function will call resolve() if the user is successfully signed
     *      in.
     *
     * @return undefined
     */
    async function tryToSignInViaAjax(
        emailAddress,
        password,
        resolve
    ) {
        try {
            let response = await Colby.callAjaxFunction(
                "CBUser",
                "signIn",
                {
                    emailAddress,
                    password,
                }
            );

            if (response.succeeded === true) {
                resolve();
            } else {
                CBUIPanel.displayCBMessage(
                    response.cbmessage
                );
            }
        } catch (error) {
            CBErrorHandler.displayAndReport(error);
        }
    }
    /* tryToSignInViaAjax() */

})();
