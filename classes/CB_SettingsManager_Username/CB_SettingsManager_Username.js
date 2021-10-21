/* global
    CB_Brick_Button,
    CB_Brick_HorizontalBar10,
    CB_Brick_KeyValue,
    CB_Brick_Padding10,
    CB_Brick_Text,
    CB_Brick_TextContainer,
    CBAjax,
    CBConvert,
    CBErrorHandler,
    CBException,
    CBModel,
    CBUINavigationView,
    CBUI,
    CBUIStringEditor2,
*/

(function () {
    "use strict";

    window.CB_SettingsManager_Username = {
        CBUserSettingsManager_createElement,
    };



    /* -- functions -- */



    /**
     * @param object args
     *
     *      {
     *          targetUserCBID: CBID
     *      }
     *
     * @return Element
     */
    function
    CBUserSettingsManager_createElement(
        args
    ) {
        let targetUserCBID = CBModel.valueAsCBID(
            args,
            "targetUserCBID"
        );

        if (targetUserCBID === null) {
            throw CBException.withValueRelatedError(
                Error(
                    "The \"targetUserCBID\" argument is not valid."
                ),
                args,
                "71e86ce7302eaa8699e0dead52aeded62402e940"
            );
        }

        let settingsManagerElement = CBUI.createElement(
            'CB_SettingsManager_Username'
        );

        let usernameKeyValueBrick = CB_Brick_KeyValue.create();

        {
            let padding10 = CB_Brick_Padding10.create();

            settingsManagerElement.append(
                padding10.CB_Brick_Padding10_getOuterElement()
            );

            let textContainer = CB_Brick_TextContainer.create();

            padding10.CB_Brick_Padding10_getInnerElement().append(
                textContainer.CB_Brick_TextContainer_getOuterElement()
            );

            textContainer.CB_Brick_TextContainer_getInnerElement().append(
                usernameKeyValueBrick.CB_Brick_KeyValue_getElement()
            );
        }

        usernameKeyValueBrick.CB_Brick_KeyValue_setHasNavigationArrow(
            true
        );

        usernameKeyValueBrick.CB_Brick_KeyValue_setKey(
            "Username"
        );

        usernameKeyValueBrick.CB_Brick_KeyValue_setValue(
            "fetching..."
        );



        (async function () {
            let usernameSettingsManagerAPI = {

                /**
                 *
                 */
                settingsManager_getTargeUserCBID(
                ) {
                    return targetUserCBID;
                },

                /**
                 *
                 */
                settingsManager_getUsername(
                ) {
                    return usernameKeyValueBrick.CB_Brick_KeyValue_getValue();
                },

                /**
                 *
                 */
                settingsManager_setUsername(
                    newUsername
                ) {
                    usernameKeyValueBrick.CB_Brick_KeyValue_setValue(
                        newUsername
                    );
                },

            };
            /* usernameSettingsManagerAPI */

            {
                let initialUsername = await fetchUsername(
                    targetUserCBID
                );

                usernameSettingsManagerAPI.settingsManager_setUsername(
                    initialUsername
                );
            }

            settingsManagerElement.addEventListener(
                "click",
                function () {
                    CBUINavigationView.navigate(
                        {
                            element: createUsernameEditorElement(
                                usernameSettingsManagerAPI
                            ),
                            left: "User",
                            title: "Change Username",
                        }
                    );
                }
            );
        })();



        return settingsManagerElement;
    }
    /* CBUserSettingsManager_createElement() */



    /**
     * @param object settingsManagerAPI
     *
     * @return Element
     */
    function
    createUsernameEditorElement(
        settingsManagerAPI
    ) {
        let usernameEditorElement = CBUI.createElement();

        let padding10 = CB_Brick_Padding10.create();

        usernameEditorElement.append(
            padding10.CB_Brick_Padding10_getOuterElement()
        );

        let textContainer = CB_Brick_TextContainer.create();

        padding10.CB_Brick_Padding10_getInnerElement().append(
            textContainer.CB_Brick_TextContainer_getOuterElement()
        );

        let text = CB_Brick_Text.create();

        textContainer.CB_Brick_TextContainer_getInnerElement().append(
            text.CB_Brick_Text_getOuterElement()
        );

        let username = settingsManagerAPI.settingsManager_getUsername();

        text.CB_Brick_Text_setText(
            `Your current username:\n${username}\n\n` +
            CBConvert.stringToCleanLine(`

                To change your username enter a new username below and press
                the "save" button. Your previous username will immediately
                become available for someone else to claim.

            `) +
            "\n\n" +
            "Usernames can use the following characters:" +
            "\n" +
            "a-z A-Z 0-9 _"
        );

        {
            let horizontalBar = CB_Brick_HorizontalBar10.create();

            textContainer.CB_Brick_TextContainer_getInnerElement().append(
                horizontalBar.CB_Brick_HorizontalBar10_getElement()
            );
        }

        let usernameStringEditor = CBUIStringEditor2.create();

        usernameStringEditor.CBUIStringEditor2_setTitle(
            "New Username"
        );

        usernameStringEditor.CBUIStringEditor2_setValue(
            settingsManagerAPI.settingsManager_getUsername()
        );

        usernameStringEditor.CBUIStringEditor2_setHasOutline(
            true
        );

        textContainer.CB_Brick_TextContainer_getInnerElement().append(
            usernameStringEditor.CBUIStringEditor2_getElement()
        );

        {
            let horizontalBar = CB_Brick_HorizontalBar10.create();

            textContainer.CB_Brick_TextContainer_getInnerElement().append(
                horizontalBar.CB_Brick_HorizontalBar10_getElement()
            );
        }

        let saveButtonBrick = CB_Brick_Button.create();

        saveButtonBrick.CB_Brick_Button_setTextContent(
            "Save"
        );

        saveButtonBrick.CB_Brick_Button_setClickedCallback(
            createUsernameEditorElement_saveUsername
        );

        textContainer.CB_Brick_TextContainer_getInnerElement().append(
            saveButtonBrick.CB_Brick_Button_getElement()
        );

        let isCurrentlySaving = false;



        /**
         * @return Promise -> undefined
         */
        async function
        createUsernameEditorElement_saveUsername(
        ) {
            if (isCurrentlySaving) {
                return;
            }

            try {
                isCurrentlySaving = true;

                saveButtonBrick.CB_Brick_Button_setIsDisabled(
                    true
                );

                let requestedUsername = (
                    usernameStringEditor.CBUIStringEditor2_getValue().trim()
                );

                let result = await CBAjax.call(
                    "CB_Username",
                    "CB_Username_ajax_setUsername",
                    {
                        CB_Username_ajax_setUsername_targetUserCBID: (
                            settingsManagerAPI.settingsManager_getTargeUserCBID()
                        ),
                        CB_Username_ajax_setUsername_requestedUsername: (
                            requestedUsername
                        ),
                    }
                );

                if (
                    result.CB_Username_ajax_setUsername_succeeded === true
                ) {
                    settingsManagerAPI.settingsManager_setUsername(
                        requestedUsername
                    );

                    window.history.back();
                } else {
                    window.alert(
                        result.CB_Username_ajax_setUsername_message
                    );
                }
            } catch (error) {
                window.alert(
                    error.message
                );
            } finally {
                isCurrentlySaving = false;

                saveButtonBrick.CB_Brick_Button_setIsDisabled(
                    false
                );
            }
        }
        /* createUsernameEditorElement_saveUsername() */



        return usernameEditorElement;
    }
    /* createUsernameEditorElement() */



    /**
     * @param CBID targetUserCBID
     *
     * @return Promise -> string
     */
    async function
    fetchUsername(
        targetUserCBID
    ) {
        try {
            let username = await CBAjax.call(
                "CB_Username",
                "CB_Username_ajax_fetchUsernameByUserCBID",
                {
                    targetUserCBID,
                }
            );

            return username;
        } catch (error) {
            CBErrorHandler.report(
                error
            );
        }
    }
    /* fetchUsername() */

})();
