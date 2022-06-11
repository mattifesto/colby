/* global
    CBMessageView,
    CBModel,
*/


(function ()
{
    "use strict";

    window.CBUserEditor =
    {
        CBUISpecEditor_createEditorElement2,
    };



    /**
     * @param Object spec
     * @param function specChangedCallback
     *
     * @return Element
     */
    function
    CBUISpecEditor_createEditorElement2(
        userSpec
        /* specChangedCallback */
    ) // -> Element
    {
        /**
         * The user model is edited differently than other models because the
         * user is able to edit their own model but should not have access to
         * edit the entire model.
         *
         * There are also a lot of other non-model data that a user has that are
         * editable. For now, we just provide a link to the user editing page
         * that uses another method of editing user models.
         */

        const userModelCBID =
        CBModel.getCBID(
            userSpec
        );

        const userEditorPageURL =
        "/admin/" +
        "?c=CBAdminPageForUserSettings" +
        `&hash=${userModelCBID}`;

        let rootElement =
        CBMessageView.create();

        rootElement.CBMessageView_setCBMessage(`

            This editing page is under construction.

            Click (here (a ${userEditorPageURL})) to edit the user.

        `);

        return rootElement;
    }
    /* CBUISpecEditor_createEditorElement() */

}
)();
