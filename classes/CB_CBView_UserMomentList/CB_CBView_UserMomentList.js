/* global
    CB_Timestamp,
    CB_CBView_MomentCreator,
    CB_Moment,
    CB_View_Moment2,
    CBAjax,
    CBConvert,
    CBErrorHandler,
    Colby,

    CBUser_currentUserModelCBID_jsvariable,
*/

(function ()
{
    "use strict";



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
        let isRenderingBatchOfOlderMoments = false;
        let oldestMomentCBTimestamp;
        let endOfOlderMomentsElementIsVisible = false;
        let hasRenderedAllMoments = false;
        let timeoutID;

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
                userModelCBID === CBUser_currentUserModelCBID_jsvariable &&
                showMomentCreator
            ) {
                let momentCreator = CB_CBView_MomentCreator.create();

                if (
                    momentCreator !== undefined
                ) {
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

            let endOfOlderMomentsElement = document.createElement(
                "div"
            );

            endOfOlderMomentsElement.className = (
                "CB_CBView_UserMomentList_endOfOlderMoments_element"
            );

            endOfOlderMomentsElement.style.height="100px";

            element.append(
                endOfOlderMomentsElement
            );

            let observer = new IntersectionObserver(
                function (
                    entries
                ) {
                    let entry = entries[0];

                    endOfOlderMomentsElementIsVisible = entry.isIntersecting;

                    if (
                        endOfOlderMomentsElementIsVisible
                    ) {
                        renderBatchOfOlderMoments();
                    }
                }
            );

            observer.observe(
                endOfOlderMomentsElement
            );
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
                if (
                    isRenderingBatchOfOlderMoments ||
                    hasRenderedAllMoments
                ) {
                    return;
                }

                isRenderingBatchOfOlderMoments = true;

                let maxUnixTimestamp;
                let maxFemtoseconds;

                if (
                    oldestMomentCBTimestamp !== undefined
                ) {
                    let decrementedCBTimestamp = CB_Timestamp.decrement(
                        oldestMomentCBTimestamp
                    );

                    maxUnixTimestamp = CB_Timestamp.getUnixTimestamp(
                        decrementedCBTimestamp
                    );

                    maxFemtoseconds = CB_Timestamp.getFemtoseconds(
                        decrementedCBTimestamp
                    );
                }

                let momentModels = await CBAjax.call(
                    "CB_Moment",
                    "fetchMomentsForUserModelCBID",
                    {
                        userModelCBID,
                        maxUnixTimestamp,
                        maxFemtoseconds,
                        maxModelsCount: 5,
                    }
                );

                if (
                    momentModels.length === 0
                ) {
                    hasRenderedAllMoments = true;

                    return;
                }

                momentModels.forEach(
                    function (
                        momentModel
                    ) {
                        oldestMomentCBTimestamp = CB_Moment.getCBTimestamp(
                            momentModel
                        );

                        renderOlderMoment(
                            momentModel,
                        );
                    }
                );

                if (
                    timeoutID === undefined
                ) {
                    timeoutID = window.setTimeout(
                        function () {
                            if (
                                endOfOlderMomentsElementIsVisible
                            ) {
                                renderBatchOfOlderMoments();
                            }

                            timeoutID = undefined;
                        },
                        10
                    );
                }
            } catch (
                error
            ) {
                CBErrorHandler.report(
                    error
                );
            } finally {
                isRenderingBatchOfOlderMoments = false;
            }
        }
        /* renderBatchOfOlderMoments() */



        /**
         * @param object newMomentModel
         *
         * @return undefined
         */
        function
        renderNewMoment(
            newMomentModelArgument
        ) {
            let moment2View = CB_View_Moment2.create();

            moment2View.CB_View_Moment2_setMomentModel(
                newMomentModelArgument
            );

            momentContainerElement.prepend(
                moment2View.CB_View_Moment2_getElement()
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
            momentModelArgument
        ) {
            let moment2View = CB_View_Moment2.create();

            moment2View.CB_View_Moment2_setMomentModel(
                momentModelArgument
            );

            momentContainerElement.append(
                moment2View.CB_View_Moment2_getElement()
            );
        }
        /* renderOlderMoment() */



        return;
    }
    /* initializeElement() */

}
)();
