"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* global
    CBAjax,
    CBModel,
    CBUI,
    CBUINavigationView,
    CBUIPanel,
    Colby,

    SCPromotionsAdmin_promotionExecutorRegistrations,
*/



(function () {

    let promotionListSectionElement;

    let currentTimestamp = Math.floor(
        Date.now() / 1000
    );


    Colby.afterDOMContentLoaded(
        afterDOMContentLoaded
    );



    /**
     * @return undefined
     */
    function afterDOMContentLoaded() {
        let elements = document.getElementsByClassName(
            "SCPromotionsAdmin"
        );

        if (elements.length > 0) {
            let element = elements.item(0);

            {
                let navigationView = CBUINavigationView.create();

                element.appendChild(
                    navigationView.element
                );
            }

            CBUINavigationView.navigate(
                {
                    element: createRootPanelElement(),
                    title: "Promotions",
                }
            );

            fetchAndRenderPromotionList();
        }
    }
    /* afterDOMContentLoaded() */



    /**
     * @return Element
     */
    function createNewPromotionButton() {
        let elements = CBUI.createElementTree(
            "CBUI_container1",
            "CBUI_button1"
        );

        let element = elements[0];
        let buttonElement = elements[1];

        buttonElement.textContent = "Create New Promotion";

        buttonElement.addEventListener(
            "click",
            function () {
                showExecutorSelector();
            }
        );

        return element;
    }
    /* createNewPromotionButton() */



    /**
     * @param object promotionModel
     *
     * @return Element
     */
    function createPromotionListSectionItemElement(
        promotionSummary
    ) {
        let statusClassName;

        if (promotionSummary.endTimestamp < currentTimestamp) {
            statusClassName = "SCPromotionsAdmin_past";
        } else if (promotionSummary.beginTimestamp < currentTimestamp) {
            statusClassName = "SCPromotionsAdmin_current";
        } else {
            statusClassName = "SCPromotionsAdmin_future";
        }

        let elements = CBUI.createElementTree(
            `CBUI_sectionItem ${statusClassName}`,
            "CBUI_container_topAndBottom",
            "SCPromotionsAdmin_promotionTitle"
        );

        let element = elements[0];

        element.addEventListener(
            "click",
            function () {
                let CBID = CBModel.valueToString(
                    promotionSummary,
                    "ID"
                );

                window.location.href = (
                    "/admin/" +
                    "?c=CBModelEditor" +
                    "&ID=" +
                    CBID
                );
            }
        );

        let textContainerElement = elements[1];
        let titleElement = elements[2];

        titleElement.textContent = CBModel.valueToString(
            promotionSummary,
            "title"
        ) || "<no title>";


        /* description */

        let descriptionElement = CBUI.createElement(
            "CBUI_textColor2 CBUI_textSize_small"
        );

        textContainerElement.appendChild(
            descriptionElement
        );

        descriptionElement.appendChild(
            Colby.unixTimestampToElement(
                promotionSummary.beginTimestamp
            )
        );

        descriptionElement.append(" -> ");

        descriptionElement.appendChild(
            Colby.unixTimestampToElement(
                promotionSummary.endTimestamp
            )
        );

        return element;
    }
    /* createPromotionListSectionItemElement() */



    /**
     * @return Element
     */
    function createRootPanelElement() {
        let elements;

        elements = CBUI.createElementTree(
            "SCPromotionsAdmin_root"
        );

        let rootPanelElement = elements[0];

        rootPanelElement.appendChild(
            createNewPromotionButton()
        );

        elements = CBUI.createElementTree(
            "CBUI_sectionContainer",
            "CBUI_section"
        );

        rootPanelElement.appendChild(
            elements[0]
        );

        promotionListSectionElement = elements[1];

        return rootPanelElement;
    }
    /* createRootPanelElement() */



    /**
     * @param string className
     *
     * @return undefined
     */
    function editNewPromotion(
        executorClassName
    ) {
        let CBID = Colby.random160();

        let suggestedSpec = {
            className: "SCPromotion",
            executor: {
                className: executorClassName,
            },
        };

        let suggestedSpecAsJSON = JSON.stringify(
            suggestedSpec
        );

        window.location.href = (
            "/admin/" +
            "?c=CBModelEditor" +
            "&ID=" +
            CBID +
            "&suggestedSpecAsJSON=" +
            encodeURIComponent(
                suggestedSpecAsJSON
            )
        );
    }
    /* editNewPromotion() */



    /**
     * @return undefined
     */
    function fetchAndRenderPromotionList() {
        CBAjax.call(
            "SCPromotionsTable",
            "fetchSummaries"
        ).then(
            function (promotionSpecs) {
                promotionSpecs.sort(
                    function (promotion1, promotion2) {
                        if (
                            promotion1.endTimestamp >
                            promotion2.endTimestamp
                        ) {
                            return -1;
                        }

                        if (
                            promotion1.endTimestamp <
                            promotion2.endTimestamp
                        ) {
                            return 1;
                        }

                        return 0;
                    }
                );

                promotionListSectionElement.textContent = "";

                for (
                    let index = 0;
                    index < promotionSpecs.length;
                    index += 1
                ) {
                    let promotionSpec = promotionSpecs[index];

                    promotionListSectionElement.appendChild(
                        createPromotionListSectionItemElement(
                            promotionSpec
                        )
                    );
                }
            }
        ).catch(
            function (error) {
                CBUIPanel.displayAndReportError(error);
            }
        );
    }
    /* fetchAndRenderPromotionList() */


    function showExecutorSelector() {
        let elements = CBUI.createElementTree(
            "SCPromotionsAdmin_executorSelector",
            "CBUI_sectionContainer",
            "CBUI_section"
        );

        let panelElement = elements[0];
        let sectionElement = elements[2];

        SCPromotionsAdmin_promotionExecutorRegistrations.forEach(
            function (registrationModel) {
                let elements = CBUI.createElementTree(
                    "CBUI_container_topAndBottom",
                    "title"
                );

                let element = elements[0];

                elements[1].textContent = registrationModel.title;

                sectionElement.appendChild(
                    element
                );

                element.addEventListener(
                    "click",
                    function () {
                        editNewPromotion(
                            registrationModel.executorClassName
                        );
                    }
                );
            }
        );

        CBUINavigationView.navigate(
            {
                element: panelElement,
                title: "Choose a Promotion Executor",
            }
        );
    }


})();
