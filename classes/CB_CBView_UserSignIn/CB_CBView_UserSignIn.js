/* global
    CB_UI_BooleanEditor,
    CB_UI_StringEditor,
    CBAjax,
    CBErrorHandler,
    CBJavaScript,
    CBMessageMarkup,
    CBUIButton,
*/


(function () {
    "use strict";



    CBJavaScript.afterDOMContentLoaded(
        function () {
            CB_CBView_UserSignIn_afterDOMContentLoaded();
        }
    );



    /**
     *  @return undefined
     */
    function
    CB_CBView_UserSignIn_afterDOMContentLoaded(
    ) {
        let placeholderElements = Array.from(
            document.getElementsByClassName(
                "CB_CBView_UserSignIn_placeholder_element"
            )
        );

        placeholderElements.forEach(
            function (
                placeholderElement
            ) {
                CB_CBView_UserSignIn_buildElement(
                    placeholderElement
                );
            }
        );
    }
    /* CB_CBView_UserSignIn_onDOMContentLoaded() */



    /**
     * @param Element placeholderElement
     *
     * @return undefined
     */
    function
    CB_CBView_UserSignIn_buildElement(
        placeholderElement
    ) {
        let rootElement = placeholderElement;

        rootElement.className = "CB_CBView_UserSignIn_root_element";



        /* message elements */

        let messageElement = document.createElement(
            "div"
        );

        messageElement.className = "CB_CBView_UserSignIn_message_element";

        rootElement.append(
            messageElement
        );

        let messageContentElement = document.createElement(
            "div"
        );

        messageContentElement.className = (
            "CB_CBView_UserSignIn_messageContent_element"
        );

        messageElement.append(
            messageContentElement
        );

        CB_CBView_UserSignIn_clearCBMessage();



        /* email address */

        let emailAddressEditor = CB_UI_StringEditor.create();

        emailAddressEditor.CB_UI_StringEditor_setInputType(
            "CB_UI_StringEditor_inputType_text"
        );

        emailAddressEditor.CB_UI_StringEditor_setName(
            "emailAddress"
        );

        emailAddressEditor.CB_UI_StringEditor_setTitle(
            "Email Address"
        );

        rootElement.append(
            emailAddressEditor.CB_UI_StringEditor_getElement()
        );

        let passwordEditor = CB_UI_StringEditor.create();

        passwordEditor.CB_UI_StringEditor_setInputType(
            "CB_UI_StringEditor_inputType_password"
        );

        passwordEditor.CB_UI_StringEditor_setName(
            "password"
        );

        passwordEditor.CB_UI_StringEditor_setTitle(
            "Password"
        );

        rootElement.append(
            passwordEditor.CB_UI_StringEditor_getElement()
        );



        let keepSignedInEditor = CB_UI_BooleanEditor.create();

        keepSignedInEditor.CB_UI_BooleanEditor_setTitle(
            "Keep signed in"
        );

        keepSignedInEditor.CB_UI_BooleanEditor_setDescription(`

            Select this option if this is a trusted device.

        `);

        rootElement.append(
            keepSignedInEditor.CB_UI_BooleanEditor_getElement()
        );



        let signInButton = CBUIButton.create();

        signInButton.CBUIButton_setTextContent(
            "Sign In"
        );

        signInButton.CBUIButton_addClickEventListener(
            function () {
                CB_CBView_UserSignIn_handleSignInButtonClickEvent();
            }
        );

        rootElement.append(
            signInButton.CBUIButton_getElement()
        );



        /**
         * @return undefined
         */
        function
        CB_CBView_UserSignIn_clearCBMessage(
        ) {
            messageElement.style.visibility = "hidden";
            messageContentElement.textContent = "";
        }
        /* CB_CBView_UserSignIn_clearMessage() */



        /**
         * @return Promise -> undefined
         */
        async function
        CB_CBView_UserSignIn_handleSignInButtonClickEvent() {
            try {
                CB_CBView_UserSignIn_clearCBMessage();

                let executorArguments = {
                    CB_Ajax_User_SignIn_emailAddress: (
                        emailAddressEditor.CB_UI_StringEditor_getValue()
                    ),

                    CB_Ajax_User_SignIn_password: (
                        passwordEditor.CB_UI_StringEditor_getValue()
                    ),

                    CB_Ajax_User_SignIn_shouldKeepSignedIn: (
                        keepSignedInEditor.CB_UI_BooleanEditor_getValue()
                    )
                };

                let response = await CBAjax.call2(
                    'CB_Ajax_User_SignIn',
                    executorArguments
                );

                if (
                    response.CB_Ajax_User_SignIn_cbmessage
                ) {
                    CB_CBView_UserSignIn_displayCBMessage(
                        response.CB_Ajax_User_SignIn_cbmessage
                    );
                } else {
                    let destinationURL = (
                        rootElement.dataset.destinationURL.trim()
                    );

                    if (
                        destinationURL === ""
                    ) {
                        window.location.reload();
                    } else {
                        window.location.href = destinationURL;
                    }
                }
            } catch (
                error
            ) {
                CBErrorHandler.report(
                    error
                );
            }
        }
        /* CB_CBView_UserSignIn_handleSignInButtonClickEvent() */



        /**
         * @param string cbmessage
         *
         * @return undefined
         */
        function
        CB_CBView_UserSignIn_displayCBMessage(
            cbmessage
        ) {
            messageContentElement.innerHTML = (
                CBMessageMarkup.messageToHTML(
                    cbmessage
                )
            );

            messageElement.style.visibility = "visible";
        }
        /* CB_CBView_UserSignIn_displayCBMessage() */

    }
    /* CB_CBView_UserSignIn_buildElement() */

})();
