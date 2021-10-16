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
        let _currentUsername;
        let _isCurrentlySaving = false;
        let _mainKeyValue = CB_Brick_KeyValue.create();
        let _saveButton = CB_Brick_Button.create();
        let _usernameEditor = CBUIStringEditor2.create();

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

        let element = CBUI.createElement(
            'CB_SettingsManager_Username'
        );

        element.addEventListener(
            "click",
            function () {
                CBUserSettingsManager_Username_showEditor(
                    targetUserCBID
                );
            }
        );

        {
            let padding10 = CB_Brick_Padding10.create();

            element.append(
                padding10.CB_Brick_Padding10_getOuterElement()
            );

            let textContainer = CB_Brick_TextContainer.create();

            padding10.CB_Brick_Padding10_getInnerElement().append(
                textContainer.CB_Brick_TextContainer_getOuterElement()
            );

            textContainer.CB_Brick_TextContainer_getInnerElement().append(
                _mainKeyValue.CB_Brick_KeyValue_getElement()
            );
        }

        _mainKeyValue.CB_Brick_KeyValue_setHasNavigationArrow(
            true
        );

        _mainKeyValue.CB_Brick_KeyValue_setKey(
            "Username"
        );

        _mainKeyValue.CB_Brick_KeyValue_setValue(
            "test"
        );

        CB_SettingsManager_Username_fetchUsername();




        /**
         * @return Promise -> undefined
         */
        async function
        CB_SettingsManager_Username_fetchUsername(
        ) {
            try {
                let username = await CBAjax.call(
                    "CB_Username",
                    "CB_Username_ajax_fetchUsernameByUserCBID",
                    {
                        targetUserCBID,
                    }
                );

                CB_SettingsManager_Username_setUsername(
                    username
                );
            } catch (error) {
                CBErrorHandler.report(
                    error
                );
            }
        }
        /* CB_SettingsManager_Username_fetchUsername() */



        /**
         * @return Promise -> undefined
         */
        async function
        CB_SettingsManager_Username_handleSaveClicked(
        ) {
            try {
                if (
                    _isCurrentlySaving === true
                ) {
                    return;
                }

                _isCurrentlySaving = true;

                _saveButton.CB_Brick_Button_setIsDisabled(
                    true
                );


                let requestedUsername = (
                    _usernameEditor.CBUIStringEditor2_getValue().trim()
                );

                let result = await CBAjax.call(
                    "CB_Username",
                    "CB_Username_ajax_setUsername",
                    {
                        CB_Username_ajax_setUsername_targetUserCBID: (
                            targetUserCBID
                        ),
                        CB_Username_ajax_setUsername_requestedUsername: (
                            requestedUsername
                        ),
                    }
                );

                if (
                    result.CB_Username_ajax_setUsername_succeeded === true
                ) {
                    CB_SettingsManager_Username_setUsername(
                        requestedUsername
                    );

                    window.history.back();
                } else {
                    window.alert(
                        result.CB_Username_ajax_setUsername_message
                    );
                }
            } catch (error) {
                window.alert("error");
            } finally {
                _isCurrentlySaving = false;

                _saveButton.CB_Brick_Button_setIsDisabled(
                    false
                );
            }
        }
        /* CB_SettingsManager_Username_handleSaveClicked() */



        function
        CB_SettingsManager_Username_setUsername(
            value
        ) {
            _currentUsername = value;

            _mainKeyValue.CB_Brick_KeyValue_setValue(
                value
            );
        }
        /* CB_SettingsManager_Username_setUsername() */



        /**
         * @return undefined
         */
        function
        CBUserSettingsManager_Username_showEditor(
        ) {
            let element = CBUI.createElement();

            let padding10 = CB_Brick_Padding10.create();

            element.append(
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

            text.CB_Brick_Text_setText(
                `Your current username:\n${_currentUsername}\n\n` +
                CBConvert.stringToCleanLine(`

                    To change your username enter a new username below and
                    press the "save" button. Your previous username will immediately
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

            _usernameEditor.CBUIStringEditor2_setTitle(
                "New Username"
            );

            _usernameEditor.CBUIStringEditor2_setValue(
                _currentUsername
            );

            _usernameEditor.CBUIStringEditor2_setHasOutline(
                true
            );

            textContainer.CB_Brick_TextContainer_getInnerElement().append(
                _usernameEditor.CBUIStringEditor2_getElement()
            );

            {
                let horizontalBar = CB_Brick_HorizontalBar10.create();

                textContainer.CB_Brick_TextContainer_getInnerElement().append(
                    horizontalBar.CB_Brick_HorizontalBar10_getElement()
                );
            }

            _saveButton.CB_Brick_Button_setTextContent(
                "Save"
            );

            _saveButton.CB_Brick_Button_setClickedCallback(
                function () {
                    CB_SettingsManager_Username_handleSaveClicked();
                }
            );

            textContainer.CB_Brick_TextContainer_getInnerElement().append(
                _saveButton.CB_Brick_Button_getElement()
            );

            CBUINavigationView.navigate(
                {
                    element,
                    left: "User",
                    title: "Change Username",
                }
            );
        }
        /* CBUserSettingsManager_Username_showEditor() */



        return element;
    }
    /* CBUserSettingsManager_createElement() */
})();
