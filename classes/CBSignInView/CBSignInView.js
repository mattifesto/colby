"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* global
    CBErrorHandler,
    CBUI,
    CBUIPanel,
    CBUIStringEditor,
    Colby,
*/



Colby.afterDOMContentLoaded(
    function afterDOMContentLoaded() {
        let viewElements = document.getElementsByClassName("CBSignInView");

        for (let index = 0; index < viewElements.length; index += 1) {
            let viewElement = viewElements.item(index);

            viewElement.appendChild(
                createSignInElement()
            );

            viewElement.appendChild(
                createCreateAccountElement()
            );
        }
        /* for */



        /**
         * @return Element
         */
        function createCreateAccountElement() {
            let accountInformationIsValid = false;

            let element = CBUI.createElement(
                "CBSignInView_createAccount"
            );

            let createAccountTitleElement = CBUI.createElement(
                "CBUI_title1"
            );

            element.appendChild(
                createAccountTitleElement
            );

            createAccountTitleElement.textContent = "Create Account";

            let sectionContainerElement = CBUI.createElement(
                "CBUI_sectionContainer"
            );

            element.appendChild(
                sectionContainerElement
            );

            let sectionElement = CBUI.createElement(
                "CBUI_section"
            );

            sectionContainerElement.appendChild(
                sectionElement
            );

            let fullNameEditor = CBUIStringEditor.create();
            fullNameEditor.title = "full name";
            fullNameEditor.changed = function () { verify(); };

            sectionElement.appendChild(
                fullNameEditor.element
            );

            let emailEditor = CBUIStringEditor.create();
            emailEditor.title = "email";
            emailEditor.changed = function () { verify(); };

            sectionElement.appendChild(
                emailEditor.element
            );

            let password1Editor = CBUIStringEditor.create(
                {
                    inputType: "password",
                }
            );

            password1Editor.title = "password";
            password1Editor.changed = function () { verify(); };

            sectionElement.appendChild(
                password1Editor.element
            );

            let password2Editor = CBUIStringEditor.create(
                {
                    inputType: "password",
                }
            );

            password2Editor.title = "confirm password";
            password2Editor.changed = function () { verify(); };

            sectionElement.appendChild(
                password2Editor.element
            );

            /* status area */

            let statusElement = CBUI.createElement(
                "CBUI_container1"
            );

            element.appendChild(
                statusElement
            );


            /* create account button */

            let createAccountButtonContainerElement = CBUI.createElement(
                "CBUI_container1"
            );

            element.appendChild(
                createAccountButtonContainerElement
            );

            let createAccountButtonElement = CBUI.createElement(
                "CBUI_button1"
            );

            createAccountButtonContainerElement.appendChild(
                createAccountButtonElement
            );

            createAccountButtonElement.textContent = "Create Account";

            createAccountButtonElement.addEventListener(
                "click",
                function () {
                    if (accountInformationIsValid !== true) {
                        CBUIPanel.displayText(
                            "The account information is not valid."
                        );

                        return;
                    }

                    Colby.callAjaxFunction(
                        "CBUser",
                        "createAccount",
                        {
                            "email": emailEditor.value.trim(),
                            "fullName": fullNameEditor.value.trim(),
                            "password": password1Editor.value,
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
                            CBErrorHandler.displayAndReport(error);
                        }
                    );
                }
            );

            return element;



            /* -- closures -- -- -- -- -- */



            /**
             * @return void
             */
            function verify() {
                accountInformationIsValid = false;

                let isActive = false;
                let statusTexts = [];

                let fullName = fullNameEditor.value.trim();

                if (fullName !== "") {
                    isActive = true;
                } else {
                    statusTexts.push("enter a name");
                }

                let email = emailEditor.value.trim();

                if (email !== "") {
                    isActive = true;
                }

                if (/^\w+@\w+\.\w+/.test(email) === false) {
                    statusTexts.push("fix email address");
                }

                let password1 = password1Editor.value;

                if (password1 !== "") {
                    isActive = true;
                }

                let password2 = password2Editor.value;

                if (password1 !== "") {
                    isActive = true;
                }

                if (password1 !== password2) {
                    statusTexts.push("no match");
                }

                if (password1 === "" && password2 === "") {
                    statusTexts.push("enter a password");
                }

                if (isActive === true) {
                    if (statusTexts.length === 0) {
                        accountInformationIsValid = true;
                    }

                    statusElement.textContent = statusTexts.join(", ");
                } else {
                    statusElement.textContent = "";
                }
            }
            /* verify() */

        }
        /* createCreateAccountElement() */



        /**
         * @return Element
         */
        function createSignInElement() {
            let element = CBUI.createElement(
                "CBSignInView_signIn",
                "form"
            );

            element.onsubmit = function (event) {
                event.preventDefault();

                signIn();
            };

            let signInTitleElement = CBUI.createElement(
                "CBUI_title1"
            );

            element.appendChild(
                signInTitleElement
            );

            signInTitleElement.textContent = "Sign In";

            let elements = CBUI.createElementTree(
                "CBUI_sectionContainer",
                "CBUI_section"
            );

            element.appendChild(
                elements[0]
            );

            let sectionElement = elements[1];

            let emailEditor = CBUIStringEditor.create();
            emailEditor.title = "email";

            sectionElement.appendChild(
                emailEditor.element
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

            let signInButtonContainerElement = CBUI.createElement(
                "CBUI_container1"
            );

            element.appendChild(
                signInButtonContainerElement
            );

            let signInButtonElement = CBUI.createElement(
                "CBUI_button1"
            );

            signInButtonContainerElement.appendChild(
                signInButtonElement
            );

            signInButtonElement.textContent = "Sign In";
            signInButtonElement.tabIndex = 0;

            signInButtonElement.addEventListener(
                "keydown",
                function (event) {
                    if (event.key === "Enter") {
                        signInButtonElement.blur();

                        signIn();
                    }
                }
            );

            signInButtonElement.addEventListener(
                "click",
                function () {
                    signIn();
                }
            );

            return element;



            /* -- closures -- -- -- -- -- */



            /**
             * @return undefined
             */
            function signIn() {
                Colby.callAjaxFunction(
                    "CBUser",
                    "signIn",
                    {
                        email: emailEditor.value.trim(),
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
                        CBErrorHandler.displayAndReport(error);
                    }
                );
            }
            /* signIn() */

        }
        /* createSignInElement() */
    }
    /* afterDOMContentLoaded() */
);
