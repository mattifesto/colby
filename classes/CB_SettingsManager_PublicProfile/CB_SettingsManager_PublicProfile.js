/* global
    CB_Brick_OnOff,
    CB_Brick_Padding10,
    CB_Brick_TextContainer,
    CBAjax,
    CBConvert,
    CBErrorHandler,
    CBException,
    CBModel,
    CBUI,
*/

(function () {
    "use strict";

    window.CB_SettingsManager_PublicProfile = {
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
                "20ce9a00f01e0e2e4aa4f97a372233220cf439fd"
            );
        }

        let settingsManagerElement = CBUI.createElement(
            'CB_SettingsManager_PublicProfile'
        );

        let publicProfileOnOffBrick = CB_Brick_OnOff.create();

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
                publicProfileOnOffBrick.CB_Brick_OnOff_getElement()
            );
        }

        publicProfileOnOffBrick.CB_Brick_OnOff_setTitle(
            "Enable Public Profile"
        );

        publicProfileOnOffBrick.CB_Brick_OnOff_setDescription(
            CBConvert.stringToCleanLine(`

                You must turn on your public profile to participate in the
                social media features of this website.

            `)
        );

        publicProfileOnOffBrick.CB_Brick_OnOff_setIsDisabled(
            true
        );

        publicProfileOnOffBrick.CB_Brick_OnOff_setChangedCallack(
            async function () {
                try {
                    publicProfileOnOffBrick.CB_Brick_OnOff_setIsDisabled(
                        true
                    );

                    let newPublicProfileIsEnabledValue = (
                        publicProfileOnOffBrick.CB_Brick_OnOff_getIsOn()
                    );

                    await CBAjax.call(
                        "CBUser",
                        "setPublicProfileIsEnabled",
                        {
                            targetUserCBID,
                            newPublicProfileIsEnabledValue,
                        }
                    );
                } catch (error) {
                    CBErrorHandler.report(
                        error
                    );
                } finally {
                    publicProfileOnOffBrick.CB_Brick_OnOff_setIsDisabled(
                        false
                    );
                }
            }
        );

        (async function () {

            let publicProfileIsEnabled = (
                await fetchPublicProfileIsEnabled(
                    targetUserCBID
                )
            );

            publicProfileOnOffBrick.CB_Brick_OnOff_setIsOn(
                publicProfileIsEnabled
            );

            publicProfileOnOffBrick.CB_Brick_OnOff_setIsDisabled(
                false
            );

        })();

        return settingsManagerElement;
    }
    /* CBUserSettingsManager_createElement() */



    /**
     * @param CBID targetUserCBID
     *
     * @return bool
     */
    async function
    fetchPublicProfileIsEnabled(
        targetUserCBID
    ) {
        try {
            let publicProfileIsEnabled = await CBAjax.call(
                "CBUser",
                "fetchPublicProfileIsEnabled",
                {
                    targetUserCBID,
                }
            );

            return publicProfileIsEnabled;
        } catch (error) {
            CBErrorHandler.report(
                error
            );
        }
    }
    /* fetchPublicProfileIsEnabled() */

})();
