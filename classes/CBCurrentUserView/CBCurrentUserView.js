"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* global
    CBErrorHandler,
    CBUI,
    CBUIPanel,
    CBUIStringEditor,
    Colby,

    CBCurrentUserView_userCBID,
    CBCurrentUserView_initialUserEmail,
    CBCurrentUserView_initialUserFullName,
*/



Colby.afterDOMContentLoaded(
    function afterDOMContentLoaded() {
        if (CBCurrentUserView_userCBID === null) {
            return;
        }

        let viewElements = document.getElementsByClassName(
            "CBCurrentUserView"
        );

        for (let index = 0; index < viewElements.length; index += 1) {
            let viewElement = viewElements.item(index);

            viewElement.appendChild(
                createFullNameEditorElement()
            );

            if (CBCurrentUserView_initialUserEmail === "") {
                viewElement.appendChild(
                    createAddEmailElement()
                );
            } else {
                viewElement.appendChild(
                    createEmailEditorElement()
                );
            }
        }
        /* for */



        /* -- closures -- -- -- -- -- */



        function createAddEmailElement() {
            let element = CBUI.createElement(
                "CBCurrentUserView_emailEditor"
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

            return element;



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
                        CBErrorHandler.displayAndReport(error);
                    }
                );
            }
            /* addEmailAddress() */

        }
        /* createAddEmailElement() */



        /**
         * @return Element
         */
        function createEmailEditorElement() {
            let element = CBUI.createElement(
                "CBCurrentUserView_emailEditor"
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

            if (CBCurrentUserView_initialUserEmail === "") {
                emailElement.textContent = Colby.nonBreakingSpace;
            } else {
                emailElement.textContent = CBCurrentUserView_initialUserEmail;
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

            return element;



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
                        CBErrorHandler.displayAndReport(error);
                    }
                );
            }
        }
        /* createEmailEditorElement() */



        /**
         * @return Element
         */
        function createFullNameEditorElement() {
            let element = CBUI.createElement(
                "CBCurrentUserView_fullNameEditor"
            );

            element.appendChild(
                CBUI.cbmessageToElement(
                    `
                        (Full Name (b))((br))
                        You can edit your full name below.
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

            let fullNameEditor = CBUIStringEditor.create();
            fullNameEditor.title = "Full Name";
            fullNameEditor.value = CBCurrentUserView_initialUserFullName;

            elements[1].appendChild(
                fullNameEditor.element
            );

            let hasChanged = false;
            let isSaving = false;
            let timeoutID;

            fullNameEditor.changed = function () {
                hasChanged = true;

                if (isSaving) {
                    return;
                }

                fullNameEditor.title = "Full Name (changed...)";

                if (timeoutID !== undefined) {
                    window.clearTimeout(timeoutID);
                }

                timeoutID = window.setTimeout(
                    function () {
                        timeoutID = undefined;

                        updateFullName();
                    },
                    1000
                );
            };

            return element;



            /* -- closures -- -- -- -- -- */



            /**
             * @return undefined
             */
            function updateFullName() {
                hasChanged = false;
                isSaving = true;

                fullNameEditor.title = "Full Name (saving...)";

                Colby.callAjaxFunction(
                    "CBUser",
                    "updateFullName",
                    {
                        fullName: fullNameEditor.value,
                    }
                ).then(
                    function () {
                        isSaving = false;

                        if (hasChanged) {
                            updateFullName();
                        } else {
                            fullNameEditor.title = "Full Name";
                        }
                    }
                ).catch(
                    function (error) {
                        CBErrorHandler.displayAndReport(error);
                    }
                );
            }
            /* updateFullName() */

        }
        /* createFullNameEditorElement() */

    }
    /* afterDOMContentLoaded() */
);
