/* global
    CB_UI_StringEditor,
    CBAjax,
    CBException,
    CBModel,
    CBUIPanel,
*/


(function () {
    "use strict";



    /* public API */

    window.CBFullNameUserSettingsManager = {
        CBUserSettingsManager_createElement,
    };



    let fullNameEditor;
    let hasChanged = false;
    let isSaving = false;
    let targetUserCBID;
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
        targetUserCBID = CBModel.valueAsCBID(
            args,
            "targetUserCBID"
        );

        if (
            targetUserCBID === null
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
        "CBFullNameUserSettingsManager";


        fullNameEditor =
        CB_UI_StringEditor.create();

        rootElement.append(
            fullNameEditor.CB_UI_StringEditor_getElement()
        );

        fullNameEditor.CB_UI_StringEditor_setTitle(
            "Full Name"
        );

        (async function ()
        {
            try
            {
                let result =
                await CBAjax.call(
                    "CBFullNameUserSettingsManager",
                    "fetchFullName",
                    {
                        targetUserCBID,
                    }
                );

                initialize(
                    result
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
     * @param object result
     *
     * @return undefined
     */
    function initialize(
        result
    ) {
        if (result.accessWasDenied) {
            fullNameEditor.CB_UI_StringEditor_setValue(
                "access was denied"
            );

            // @TODO replace editor with non-editor

            return;
        }

        fullNameEditor.CB_UI_StringEditor_setValue(
            result.targetUserFullName
        );

        fullNameEditor.CB_UI_StringEditor_setChangedEventListener(
            function (
            ) // -> undefined
            {
                hasChanged = true;

                if (
                    isSaving
                ) {
                    return;
                }

                fullNameEditor.CB_UI_StringEditor_setTitle(
                    "Full Name (changed...)"
                );

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

                        await updateFullName();
                    },
                    1000
                );
            }
        );
    }
    /* initialize() */



    /**
     * @return undefined
     */
    async function
    updateFullName(
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

            await CBAjax.call(
                "CBFullNameUserSettingsManager",
                "updateFullName",
                {
                    targetUserCBID,

                    targetUserFullName:
                    fullNameEditor.CB_UI_StringEditor_getValue(),
                }
            );

            if (
                hasChanged
            ) {
                updateFullName();
            }

            else
            {
                fullNameEditor.CB_UI_StringEditor_setTitle(
                    "Full Name"
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
    /* updateFullName() */

})();
