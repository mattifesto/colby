/* global
    CB_Brick_TextContainer,
    CB_CBView_MostRecentUserMoment,
    CBAjax,
    CBConvert,
    CBErrorHandler,
    CBModel,
    CBUIStringEditor2,
 */


(function () {
    "use strict";

    let publicProfileCache = {};

    window.CB_CBView_MostRecentUserMomentEditor = {
        CBUISpecEditor_createEditorElement,
        CBUISpec_toDescription,
    };



    /**
     * @param object args
     *
     *      {
     *          spec: object
     *          specChangedCallback: function
     *      }
     *
     * @return Element
     */
    function
    CBUISpecEditor_createEditorElement(
        args
    ) {
        let spec = args.spec;
        let specChangedCallback = args.specChangedCallback;
        let isCurrentlyLookingUpUsername = false;
        let usernameHasChanged = false;

        let rootEditorElement = document.createElement(
            "div"
        );

        rootEditorElement.className = "CB_CBView_UserMomentListEditor CB_UI";

        let textContainer = CB_Brick_TextContainer.create();

        rootEditorElement.append(
            textContainer.CB_Brick_TextContainer_getOuterElement()
        );

        let usernameEditor = CBUIStringEditor2.create();

        usernameEditor.CBUIStringEditor2_setHasOutline(
            true
        );

        usernameEditor.CBUIStringEditor2_setTitle(
            "Username"
        );

        (async function () {
            try {
                let userModelCBID = CBModel.valueAsCBID(
                    spec,
                    "CB_CBView_MostRecentUserMoment_userModelCBID"
                );

                if (
                    userModelCBID === undefined
                ) {
                    return;
                }

                let publicProfile = await fetchPublicProfileFromCache(
                    userModelCBID
                );

                usernameEditor.CBUIStringEditor2_setValue(
                    publicProfile.CBUser_publicProfile_prettyUsername
                );
            } catch (
                error
            ) {
                CBErrorHandler.report(
                    error
                );
            }
        })();

        usernameEditor.CBUIStringEditor2_setChangedEventListener(
            function () {
                handleUsernameChanged();
            }
        );

        textContainer.CB_Brick_TextContainer_getInnerElement().append(
            usernameEditor.CBUIStringEditor2_getElement()
        );



        /**
         * @return Promise -> undefined
         */
        async function
        handleUsernameChanged(
        ) // -> Promise -> undefined
        {
            try
            {
                if (
                    isCurrentlyLookingUpUsername
                ) {
                    usernameHasChanged =
                    true;

                    return;
                }

                usernameEditor.CBUIStringEditor2_setTitle(
                    "Username (looking up...)"
                );

                let prettyUsername =
                usernameEditor.CBUIStringEditor2_getValue();

                let result =
                await CBAjax.call2(
                    "CB_Ajax_User_PrettyUsernameToUserModelCBID",
                    {
                        CB_Ajax_User_PrettyUsernameToUserModelCBID_prettyUsername_argument:
                        prettyUsername,
                    }
                );

                let userModelCBID =
                result.CB_Ajax_User_PrettyUsernameToUserModelCBID_userModelCBID;

                usernameEditor.CBUIStringEditor2_setTitle(
                    userModelCBID === null ?
                    "Username (not found)" :
                    "Username (found)"
                );

                CB_CBView_MostRecentUserMoment.setUserModelCBID(
                    spec,
                    userModelCBID
                );

                isCurrentlyLookingUpUsername =
                false;

                if (
                    usernameHasChanged
                ) {
                    usernameHasChanged =
                    false;

                    handleUsernameChanged();
                }

                else
                {
                    specChangedCallback();
                }
            }

            catch (
                error
            ) {
                CBErrorHandler.report(
                    error
                );
            }
        }
        /* handleUsernameChanged() */



        return rootEditorElement;
    }
    /* CBUISpecEditor_createEditorElement() */



    /**
     * @param object viewSpec
     *
     * @return string
     */
    async function
    CBUISpec_toDescription(
        viewSpec
    ) {
        try {
            let userModelCBID = CBModel.valueAsCBID(
                viewSpec,
                "CB_CBView_MostRecentUserMoment_userModelCBID"
            );

            if (
                userModelCBID === undefined
            ) {
                return "no user has been selected";
            }

            let publicProfile = await fetchPublicProfileFromCache(
                userModelCBID
            );

            return CBConvert.stringToCleanLine(`

                ${publicProfile.CBUser_publicProfile_fullName}

                (${publicProfile.CBUser_publicProfile_prettyUsername})

            `);
        } catch (
            error
        ) {
            CBErrorHandler.report(
                error
            );
        }
    }
    /* CBUISpec_toDescription() */



    /**
     * @param CBID userModelCBID
     *
     * @return object
     */
    async function
    fetchPublicProfileFromCache(
        userModelCBID
    ) {
        try {
            if (
                publicProfileCache[userModelCBID] === undefined
            ) {
                let publicProfile = await CBAjax.call(
                    "CBUser",
                    "fetchPublicProfileByUserModelCBID",
                    {
                        userModelCBID,
                    }
                );

                publicProfileCache[userModelCBID] = publicProfile;
            }

            return publicProfileCache[userModelCBID];
        } catch (
            error
        ) {
            CBErrorHandler.report(
                error
            );
        }
    }
    /* fetchPublicProfileFromCache() */

})();
