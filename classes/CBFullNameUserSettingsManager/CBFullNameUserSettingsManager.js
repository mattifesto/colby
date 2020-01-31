"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* global
    CBErrorHandler,
    CBException,
    CBModel,
    CBUI,
    CBUIStringEditor,
    Colby,
*/



(function () {

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

        let elements = CBUI.createElementTree(
            "CBFullNameUserSettingsManager",
            "CBUI_sectionContainer",
            "CBUI_section"
        );

        let element = elements[0];
        let sectionElement = elements[2];

        fullNameEditor = CBUIStringEditor.create();

        sectionElement.appendChild(
            fullNameEditor.element
        );

        fullNameEditor.title = "Full Name";

        Colby.callAjaxFunction(
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
                CBErrorHandler.displayAndReport(error);
            }
        );

        return element;
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
            fullNameEditor.value = "access was denied";

            // @TODO replace editor with non-editor

            return;
        }

        fullNameEditor.value = result.targetUserFullName;

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
    }
    /* initialize() */



    /**
     * @return undefined
     */
    function updateFullName() {
        hasChanged = false;
        isSaving = true;

        fullNameEditor.title = "Full Name (saving...)";

        Colby.callAjaxFunction(
            "CBFullNameUserSettingsManager",
            "updateFullName",
            {
                targetUserCBID,
                targetUserFullName: fullNameEditor.value,
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

})();
