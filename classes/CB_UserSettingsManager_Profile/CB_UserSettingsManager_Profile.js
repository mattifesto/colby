/* global
    CB_Link_ArrayEditor,
    CB_UI_StringEditor,
    CB_UI_ImageChooser,
    CBAjax,
    CBException,
    CBModel,
    CBUIPanel,
*/


(function ()
{
    "use strict";



    let CB_UserSettingsManager_Profile =
    {
        CBUserSettingsManager_createElement,
    };

    window.CB_UserSettingsManager_Profile =
    CB_UserSettingsManager_Profile;



    let shared_profileImageChooser;
    let shared_profileImageModel;

    let bioEditor;
    let fullNameEditor;
    let profileLinkArrayEditor;

    let hasChanged = false;
    let isSaving = false;
    let targetUserModelCBID;
    let timeoutID;



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
    ) // -> Element
    {
        targetUserModelCBID = CBModel.valueAsCBID(
            args,
            "targetUserCBID"
        );

        if (
            targetUserModelCBID === null
        ) {
            throw CBException.withValueRelatedError(
                Error("The \"targetUserCBID\" argument is not valid."),
                args,
                "97cb01e065ab25ed06fa2f3e604e710353d9a2c1"
            );
        }

        let rootElement =
        document.createElement(
            "div"
        );

        rootElement.className =
        "CB_UserSettingsManager_Profile";



        shared_profileImageChooser =
        CB_UserSettingsManager_Profile_createImageChooser(
            rootElement
        );



        // full name

        fullNameEditor =
        CB_UI_StringEditor.create();

        rootElement.append(
            fullNameEditor.CB_UI_StringEditor_getElement()
        );

        fullNameEditor.CB_UI_StringEditor_setTitle(
            "Full Name"
        );



        // bio

        bioEditor =
        CB_UI_StringEditor.create();

        rootElement.append(
            bioEditor.CB_UI_StringEditor_getElement()
        );

        bioEditor.CB_UI_StringEditor_setTitle(
            "Bio"
        );



        // profile link array

        profileLinkArrayEditor =
        CB_Link_ArrayEditor.create();

        rootElement.append(
            profileLinkArrayEditor.CB_Link_ArrayEditor_getElement()
        );

        profileLinkArrayEditor.CB_Link_ArrayEditor_setTitle(
            "Profile Links"
        );



        (async function ()
        {
            try
            {
                let userProfile =
                await CBAjax.call2(
                    "CB_Ajax_User_FetchProfile",
                    {
                        CB_Ajax_User_FetchProfile_targetUserModelCBID_argument:
                        targetUserModelCBID,
                    }
                );

                initialize(
                    userProfile
                );
            }

            catch (
                error
            ) {
                CBUIPanel.displayAndReportError(
                    error
                );
            }
        }
        )();


        return rootElement;
    }
    /* CBUserSettingsManager_createElement() */



    /**
     * @param Element parentElementArgument
     *
     * @return <CB_UI_ImageChooser controller>
     */
    function
    CB_UserSettingsManager_Profile_createImageChooser(
        parentElementArgument
    ) // -> object
    {
        let imageChooser =
        CB_UI_ImageChooser.create();

        parentElementArgument.append(
            imageChooser.CB_UI_ImageChooser_getElement()
        );

        imageChooser.CB_UI_ImageChooser_setImageWasChosenCallback(
            CB_UserSettingsManager_Profile_imageWasChosenCallback
        );

        imageChooser.CB_UI_ImageChooser_setImageWasRemovedCallback(
            CB_UserSettingsManager_Profile_imageWasRemovedCallback
        );

        imageChooser.CB_UI_ImageChooser_setTitle(
            'Profile Image'
        );

        return imageChooser;
    }
    // CB_UserSettingsManager_Profile_createImageChooser()



    /**
     * @return Promise -> undefined
     */
    async function
    CB_UserSettingsManager_Profile_imageWasChosenCallback(
    ) // Promise -> undefined
    {
        try
        {
            shared_profileImageChooser.CB_UI_ImageChooser_setTitle(
                'uploading...'
            );

            shared_profileImageModel =
            await
            CBAjax.call(
                "CBImages",
                "upload",
                {},
                shared_profileImageChooser.CB_UI_ImageChooser_getImageFile()
            );

            /**
             * @NOTE 2022_11_01_1667322021
             *
             *      The image chooser title and image will be reset when the
             *      profile is successfuly saved.
             */

            scheduleProfileSave();
        }

        catch (
            error
        ) {
            CBUIPanel.displayAndReportError(
                error
            );
        }
    }
    // CB_UserSettingsManager_Profile_imageWasChosenCallback()



    /**
     * @return Promise -> undefined
     */
    async function
    CB_UserSettingsManager_Profile_imageWasRemovedCallback(
    ) // Promise -> undefined
    {
        try
        {
            shared_profileImageChooser.CB_UI_ImageChooser_setTitle(
                'removing...'
            );

            shared_profileImageModel =
            undefined;

            /**
             * @NOTE 2022_11_01_1667322021
             *
             *      The image chooser title and image will be reset when the
             *      profile is successfuly saved.
             */

            scheduleProfileSave();
        }

        catch (
            error
        ) {
            CBUIPanel.displayAndReportError(
                error
            );
        }
    }
    // CB_UserSettingsManager_Profile_imageWasRemovedCallback()



    /**
     * @param object result
     *
     * @return undefined
     */
    function
    initialize(
        userProfile
    ) // -> undefined
    {
        fullNameEditor.CB_UI_StringEditor_setValue(
            userProfile.CB_Ajax_User_FetchProfile_fullName
        );

        fullNameEditor.CB_UI_StringEditor_setChangedEventListener(
            function (
            ) // -> undefined
            {
                fullNameEditor.CB_UI_StringEditor_setTitle(
                    "Full Name (changed...)"
                );

                scheduleProfileSave();
            }
        );

        initializeBio(
            userProfile
        );

        CB_UserSettingsManager_Profile_initializeProfileImageModel(
            userProfile
        );

        initializeProfileLinkArray(
            userProfile
        );
    }
    /* initialize() */



    /**
     * @param object userProfile
     *
     * @return undefined
     */
    function
    initializeBio(
        userProfile
    ) // -> undefined
    {
        bioEditor.CB_UI_StringEditor_setValue(
            userProfile.CB_Ajax_User_FetchProfile_bio
        );

        bioEditor.CB_UI_StringEditor_setChangedEventListener(
            function (
            ) // -> undefined
            {
                bioEditor.CB_UI_StringEditor_setTitle(
                    "Bio (changed...)"
                );

                scheduleProfileSave();
            }
        );
    }
    // initializeBio()



    /**
     * @param object userProfileArgument
     *
     * @return undefined
     */
    function
    CB_UserSettingsManager_Profile_initializeProfileImageModel(
        userProfileArgument
    ) // -> undefined
    {
        shared_profileImageModel =
        userProfileArgument.CB_Ajax_User_FetchProfile_profileImageModel;

        shared_profileImageChooser.CB_UI_ImageChooser_setImage(
            shared_profileImageModel
        );
    }
    // CB_Ajax_User_FetchProfile_profileImageModel()



    /**
     * @param object userProfile
     *
     * @return undefined
     */
    function
    initializeProfileLinkArray(
        userProfile
    ) // -> undefined
    {
        profileLinkArrayEditor.CB_Link_ArrayEditor_setValue(
            userProfile.CB_Ajax_User_FetchProfile_profileLinkArray
        );

        profileLinkArrayEditor.CB_Link_ArrayEditor_setChangedEventListener(
            function (
            ) // -> undefined
            {
                profileLinkArrayEditor.CB_Link_ArrayEditor_setTitle(
                    "Profile Links (changed...)"
                );

                scheduleProfileSave();
            }
        );
    }
    // initializeProfileLinkArray()



    /**
     * @return undefined
     */
    async function
    executeProfileSave(
    ) // -> Promise -> undefined
    {
        try
        {
            hasChanged =
            false;

            isSaving =
            true;

            fullNameEditor.CB_UI_StringEditor_setTitle(
                "Full Name (saving...)"
            );

            bioEditor.CB_UI_StringEditor_setTitle(
                "Bio (saving...)"
            );

            profileLinkArrayEditor.CB_Link_ArrayEditor_setTitle(
                "Profile Links (saving...)"
            );

            await CBAjax.call2(
                "CB_Ajax_User_UpdateProfile",
                {
                    CB_Ajax_User_UpdateProfile_targetUserModelCBID_argument:
                    targetUserModelCBID,

                    CB_Ajax_User_UpdateProfile_targetUserBio_argument:
                    bioEditor.CB_UI_StringEditor_getValue(),

                    CB_Ajax_User_UpdateProfile_targetUserFullName_argument:
                    fullNameEditor.CB_UI_StringEditor_getValue(),

                    CB_Ajax_User_UpdateProfile_targetUserProfileImageModel_argument:
                    shared_profileImageModel,

                    CB_Ajax_User_UpdateProfile_targetUserProfileLinkArray_argument:
                    profileLinkArrayEditor.CB_Link_ArrayEditor_getValue(),
                }
            );

            if (
                hasChanged
            ) {
                await executeProfileSave();
            }

            else
            {
                fullNameEditor.CB_UI_StringEditor_setTitle(
                    "Full Name"
                );

                bioEditor.CB_UI_StringEditor_setTitle(
                    "Bio"
                );

                profileLinkArrayEditor.CB_Link_ArrayEditor_setTitle(
                    "Profile Links"
                );

                shared_profileImageChooser.CB_UI_ImageChooser_setTitle(
                    'Profile Image'
                );

                shared_profileImageChooser.CB_UI_ImageChooser_setImage(
                    shared_profileImageModel
                );
            }
        }

        catch (
            error
        ) {
            CBUIPanel.displayAndReportError(
                error
            );
        }

        finally
        {
            isSaving =
            false;
        }
    }
    /* executeProfileSave() */



    /**
     * @return undefined
     */
    function
    scheduleProfileSave(
    ) // -> undefined
    {
        hasChanged = true;

        if (
            isSaving
        ) {
            return;
        }

        if (
            timeoutID !== undefined
        ) {
            window.clearTimeout(
                timeoutID
            );
        }

        timeoutID =
        window.setTimeout(
            async function (
            ) // -> Promise -> undefined
            {
                timeoutID =
                undefined;

                executeProfileSave();
            },
            1000
        );
    }
    // scheduleProfileSave()

}
)();
