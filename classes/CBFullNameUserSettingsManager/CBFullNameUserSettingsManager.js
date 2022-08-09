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
    function CBUserSettingsManager_createElement(
        args
    ) {
        targetUserCBID = CBModel.valueAsCBID(
            args,
            "targetUserCBID"
        );

        if (targetUserCBID === null) {
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

        CBAjax.call(
            "CBFullNameUserSettingsManager",
            "fetchFullName",
            {
                targetUserCBID,
            }
        ).then(
            function (result) {
                initialize(result);
            }
        ).catch(
            function (error) {
                CBUIPanel.displayAndReportError(error);
            }
        );

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
                    function (
                    ) // -> undefined
                    {
                        timeoutID =
                        undefined;

                        updateFullName();
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
    function
    updateFullName(
    ) // -> undefined
    {
        hasChanged =
        false;

        isSaving =
        true;

        fullNameEditor.CB_UI_StringEditor_setTitle(
            "Full Name (saving...)"
        );

        CBAjax.call(
            "CBFullNameUserSettingsManager",
            "updateFullName",
            {
                targetUserCBID,

                targetUserFullName:
                fullNameEditor.CB_UI_StringEditor_getValue(),
            }
        ).then(
            function (
            ) // -> undefined
            {
                isSaving =
                false;

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
        ).catch(
            function (
                error
            ) // -> undefined
            {
                CBUIPanel.displayAndReportError(
                    error
                );
            }
        );
    }
    /* updateFullName() */

})();
