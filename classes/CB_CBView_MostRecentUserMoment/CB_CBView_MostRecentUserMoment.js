/* global
    CB_CBView_Moment,
    CBAjax,
    CBConvert,
    CBErrorHandler,
*/


(function ()
{
    "use strict";

    window.CB_CBView_MostRecentUserMoment = {
        setUserModelCBID,
    };

    {
        let elements = Array.from(
            document.getElementsByClassName(
                "CB_CBView_MostRecentUserMoment"
            )
        );

        elements.forEach(
            function (
                element
            ) {
                initializeElement(
                    element
                );
            }
        );
    }



    /* -- accessors -- */



    /**
     * @param object viewModel
     * @param CBID|null userModelCBID
     *
     * @return undefined
     */
    function
    setUserModelCBID(
        viewModel,
        userModelCBID
    ) // -> undefined
    {
        viewModel.CB_CBView_MostRecentUserMoment_userModelCBID = userModelCBID;
    }
    /* setUserModelCBID() */



    /* -- functions -- */



    /**
     * @param Element element
     *
     * @return Promise -> undefined
     */
    async function
    initializeElement(
        element
    ) // -> Promise -> undefined
    {
        let userModelCBID;

        try {
            userModelCBID = CBConvert.valueAsCBID(
                element.dataset.userModelCBID
            );

            if (
                userModelCBID === undefined
            ) {
                return;
            }

            let momentModels = await CBAjax.call(
                "CB_Moment",
                "fetchMomentsForUserModelCBID",
                {
                    maxModelsCount: 1,
                    userModelCBID,
                }
            );

            if (
                momentModels.length > 0
            ) {
                let momentModel = momentModels[0];

                let momentView = CB_CBView_Moment.createStandardMoment(
                    momentModel
                );

                element.append(
                    momentView.CB_CBView_Moment_getElement()
                );
            }
        } catch(
            error
        ) {
            CBErrorHandler.report(
                error
            );
        }
    }
    /* initializeElement() */

})();
