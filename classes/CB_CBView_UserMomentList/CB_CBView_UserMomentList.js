/* global
    CB_CBView_Moment,
    CB_CBView_MomentCreator,
    CBAjax,
    CBConvert,
    CBErrorHandler,
    Colby,

    CUser_currentUserModelCBID_jsvariable,
*/

(function () {

    Colby.afterDOMContentLoaded(
        function () {
            initialize();
        }
    );



    /* -- functions -- */



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
        let userModelCBID;
        let momentContainerElement;

        try {
            let showMomentCreator = CBConvert.valueToBool(
                element.dataset.showMomentCreator
            );

            userModelCBID = CBConvert.valueAsCBID(
                element.dataset.userModelCBID
            );

            if (
                userModelCBID === undefined
            ) {
                return;
            }

            if (
                userModelCBID !== undefined &&
                userModelCBID === CUser_currentUserModelCBID_jsvariable &&
                showMomentCreator
            ) {
                let momentCreator = CB_CBView_MomentCreator.create();

                let momentCreatorElement = (
                    momentCreator.CB_CBView_MomentCreator_getElement()
                );

                if (
                    momentCreatorElement !== undefined
                ) {
                    element.append(
                        momentCreatorElement
                    );

                    momentCreator.CB_CBView_MomentCreator_setNewMomentCallback(
                        function (
                            newMomentModel
                        ) {
                            renderNewMoment(
                                newMomentModel
                            );
                        }
                    );
                }
            }

            momentContainerElement = document.createElement(
                "div"
            );

            momentContainerElement.className = (
                "CB_CBView_UserMomentList_momentContainer"
            );


            element.append(
                momentContainerElement
            );

            renderBatchOfOlderMoments();
        } catch(
            error
        ) {
            CBErrorHandler.report(
                error
            );
        }



        /**
         * @return Promise -> undefined
         */
        async function
        renderBatchOfOlderMoments(
        ) {
            try {
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
                        renderOlderMoment(
                            momentModel,
                        );
                    }
                );
            } catch (error) {
                CBErrorHandler.report(
                    error
                );
            }
        }
        /* handleTimeout() */



        /**
         * @param object newMomentModel
         *
         * @return undefined
         */
        function
        renderNewMoment(
            newMomentModel
        ) {
            let momentView = CB_CBView_Moment.createStandardMoment(
                newMomentModel
            );

            momentContainerElement.prepend(
                momentView.CB_CBView_Moment_getElement()
            );
        }
        /* renderNewMoment() */



        /**
         * @param object momentModel
         *
         * @return undefined
         */
        function
        renderOlderMoment(
            momentModel,
        ) {
            let momentView = CB_CBView_Moment.createStandardMoment(
                momentModel
            );

            momentContainerElement.append(
                momentView.CB_CBView_Moment_getElement()
            );
        }
        /* renderOlderMoment() */

        return;
    }
    /* initializeElement() */

})();
