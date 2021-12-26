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
     * @return Promise -> undefined
     */
    async function
    fetchMomentModels(
        userModelCBID,
    ) {
        try {
            let momentModels = await CBAjax.call(
                "CB_Moment",
                "fetchMomentsForUserModelCBID",
                {
                    userModelCBID,
                }
            );

            return momentModels;
        } catch (error) {
            CBErrorHandler.report(
                error
            );
        }
    }



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

        let momentContainerElement = document.createElement(
            "div"
        );

        momentContainerElement.className = (
            "CB_CBView_UserMomentList_momentContainer"
        );

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

                {
                    let momentCreatorElement = (
                        momentCreator.CB_CBView_MomentCreator_getElement()
                    );

                    if (momentCreatorElement !== undefined) {
                        element.append(
                            momentCreatorElement
                        );

                        momentCreator.CB_CBView_MomentCreator_setNewMomentCallback(
                            function (
                                newMomentModel
                            ) {
                                let momentView = CB_CBView_Moment.createStandardMoment(
                                    newMomentModel
                                );

                                momentContainerElement.prepend(
                                    momentView.CB_CBView_Moment_getElement()
                                );
                            }
                        );
                    }
                }
            }

            element.append(
                momentContainerElement
            );

            handleTimeout();
        } catch(
            error
        ) {
            CBErrorHandler.report(
                error
            );
        }



        /**
         * @return undefined
         */
        async function
        handleTimeout(
        ) {
            let momentModels = await fetchMomentModels(
                userModelCBID
            );

            momentModels.forEach(
                function (
                    momentModel
                ) {
                    renderMoment(
                        momentModel,
                        momentContainerElement
                    );
                }
            );
        }
        /* handleTimeout() */

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
