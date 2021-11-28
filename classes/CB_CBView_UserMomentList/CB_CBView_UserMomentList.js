/* global
    CB_CBView_Moment,
    CBAjax,
    CBErrorHandler,
    Colby,
*/

(function () {

    Colby.afterDOMContentLoaded(
        function () {
            initialize();
        }
    );



    /**
     * @return undefined
     */
    function
    initialize(
    ) {
        let elements = Array.from(
                document.getElementsByClassName(
                "CB_CBView_UserMomentList"
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
    /* initialize() */



    /**
     * @param Element element
     *
     * @return Promise -> undefined
     */
    async function
    initializeElement(
        element
    ) {
        try {
            let userModelCBID = element.dataset.userCbid;

            let momentModels = await CBAjax.call(
                "CB_Moment",
                "fetchMomentsForUserModelCBID",
                {
                    userModelCBID,
                }
            );

            momentModels.forEach(
                function (
                    momentModel
                ) {
                    renderMoment(
                        momentModel,
                        element
                    );
                }
            );
        } catch (error) {
            CBErrorHandler.report(
                error
            );
        }
    }
    /* initializeElement() */



    /**
     * @param object momentModel
     * @param Element parentElement
     *
     * @return undefined
     */
    function
    renderMoment(
        momentModel,
        parentElement
    ) {
        let momentView = CB_CBView_Moment.createStandardMoment(
            momentModel
        );

        parentElement.append(
            momentView.CB_CBView_Moment_getElement()
        );
    }
    /* renderMoment() */

})();
