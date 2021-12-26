/* global
    CBAjax,
    CBUIStringEditor2,
    CB_Brick_TextContainer,
    CB_CBView_MostRecentUserMoment,
 */


(function () {

    "use strict";

    window.CB_CBView_MostRecentUserMomentEditor = {
        CBUISpecEditor_createEditorElement,
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
        ) {
            if (
                isCurrentlyLookingUpUsername
            ) {
                usernameHasChanged = true;

                return;
            }

            usernameEditor.CBUIStringEditor2_setTitle(
                "Username (looking up...)"
            );

            let userModelCBID = await CBAjax.call(
                "CB_Username",
                "CB_Username_ajax_fetchUserModelCBIDByPrettyUsername",
                {
                    prettyUsername: usernameEditor.CBUIStringEditor2_getValue(),
                }
            );

            usernameEditor.CBUIStringEditor2_setTitle(
                userModelCBID === null ?
                "Username (not found)" :
                "Username (found)"
            );

            CB_CBView_MostRecentUserMoment.setUserModelCBID(
                spec,
                userModelCBID
            );

            isCurrentlyLookingUpUsername = false;

            if (usernameHasChanged) {
                usernameHasChanged = false;

                handleUsernameChanged();
            } else {
                specChangedCallback();
            }
        }
        /* handleUsernameChanged() */



        return rootEditorElement;
    }
    /* CBUISpecEditor_createEditorElement() */

})();
