/* global
    CB_Moment,
    CBImage,
    CBErrorHandler,
    CBModel,
    CBUser,
    Colby,
*/


(function () {
    "use strict";

    window.CB_CBView_Moment = {
        create,
        createStandardMoment,
    };



    /**
     * @return object (CB_CBView_Moment)
     */
    function
    create(
    ) {
        let element = document.createElement(
            "div"
        );

        element.className = "CB_CBView_Moment_root_element";

        let contentElement = document.createElement(
            "div"
        );

        contentElement.className = "CB_CBView_Moment_content_element";

        element.append(
            contentElement
        );

        let api = {
            CB_CBView_Moment_addContentClickEventListener,
            CB_CBView_Moment_getElement,
            CB_CBView_Moment_append,
        };



        /* -- accessors -- */



        /**
         * @return Element
         */
        function
        CB_CBView_Moment_getElement(
        ) {
            return element;
        }
        /* CB_CBView_Moment_getElement() */



        /* -- functions -- */



        /**
         * @param Element childElement
         *
         * @return undefined
         */
        function
        CB_CBView_Moment_append(
            childElement
        ) {
            contentElement.append(
                childElement
            );
        }
        /* CB_CBView_Moment_append() */



        /**
         * @param function callback
         *
         * @return undefined
         */
        function
        CB_CBView_Moment_addContentClickEventListener(
            callback
        ) {
            contentElement.addEventListener(
                "click",
                callback
            );
        }
        /* CB_CBView_Moment_addContentClickEventListener() */



        return api;
    }
    /* create() */



    /**
     * @param object momentModel
     *
     * @return Element
     */
    function
    createHeaderElement(
        momentModel
    ) {
        let headerElement = document.createElement(
            "div"
        );

        headerElement.className = "CB_CBView_Moment_header_element";

        populateHeaderElement(
            headerElement,
            momentModel
        );

        return headerElement;
    }
    /* createHeaderElement() */



    /**
     * @param object momentModel
     * @param bool isForMomentPage
     *
     *      If this argument is true, the moment will not have click event
     *      handlers to navigate to the moment page since we already are on the
     *      moment page.
     *
     * @return CB_CBView_Moment
     */
    function
    createStandardMoment(
        momentModel,
        isForMomentPage
    ) {
        let momentView = create();

        /**
         * If you select some of the moment text the "click" event will be
         * raised after the text is selected. This code handles the
         * "selectionchange" event by setting this variable to true so the next
         * time the code handles the "click" event it knows to ignore the click.
         *
         * This is becuase users should be able to select the text of a moment
         * without navigating to the moment page.
         */
        let ignoreClickEvent = false;

        momentView.CB_CBView_Moment_getElement().classList.add(
            "CB_CBView_Moment_standard_element"
        );

        let momentModelCBID = CBModel.getCBID(
            momentModel
        );

        if (
            isForMomentPage !== true
        ) {
            momentView.CB_CBView_Moment_addContentClickEventListener(
                function (
                ) {
                    if (
                        ignoreClickEvent
                    ) {
                        ignoreClickEvent = false;

                        return;
                    }

                    window.location.assign(
                        `/moment/${momentModelCBID}`
                    );
                }
            );

            document.addEventListener(
                "selectionchange",
                function ()
                {
                    let selection = document.getSelection();

                    /**
                     * If the selection started or ended on an element outside
                     * the view element the click event we listen for will not
                     * be raised.
                     */

                    let momentViewElement = (
                        momentView.CB_CBView_Moment_getElement()
                    );

                    let momentViewElementContainsAnchorNode =
                    momentViewElement.contains(
                        selection.anchorNode
                    );

                    let momentViewElementContainsFocusNode =
                    momentViewElement.contains(
                        selection.focusNode
                    );

                    if (
                        !momentViewElementContainsAnchorNode  ||
                        !momentViewElementContainsFocusNode
                    ) {
                        /* reset ignoreClickEvent */
                        ignoreClickEvent = false;

                        return;
                    }

                    /**
                     * If the user click on the text, that will be considered a
                     * selection and we want it to be considered a click. So if
                     * the selected text is empty, we treat it as a click.
                     */

                    let selectedText = selection.toString();

                    if (
                        selectedText === ""
                    ) {
                        /* reset ignoreClickEvent */
                        ignoreClickEvent = false;

                        return;
                    }

                    ignoreClickEvent = true;
                }
            );
        }

        momentView.CB_CBView_Moment_append(
            createHeaderElement(
                momentModel
            )
        );

        let textElement = document.createElement(
            "div"
        );

        textElement.className = "CB_CBView_Moment_text_element";

        textElement.textContent = CB_Moment.getText(
            momentModel
        );

        momentView.CB_CBView_Moment_append(
            textElement
        );

        let imageModel = CB_Moment.getImage(
            momentModel
        );

        if (
            imageModel !== undefined
        ) {
            let alternativeText =
            "Image";

            let maximumDisplayWidthInCSSPixels =
            1280;

            let maximumDisplayHeightInCSSPixels =
            500;

            let pictureElement =
            CBImage.createPictureElementWithMaximumDisplayWidthAndHeight(
                imageModel,
                "rw1280",
                maximumDisplayWidthInCSSPixels,
                maximumDisplayHeightInCSSPixels,
                alternativeText
            );

            pictureElement.className =
            "CB_CBView_Moment_picture_element";

            momentView.CB_CBView_Moment_append(
                pictureElement
            );
        }

        return momentView;
    }
    /* createStandardMoment() */



    /**
     * @param Element headerElement
     * @param object momentModel
     *
     * @return Promise -> undefined
     */
    async function
    populateHeaderElement(
        headerElement,
        momentModel
    ) {
        try {
            let publicProfile = await CBUser.fetchPublicProfileByUserModelCBID(
                CB_Moment.getAuthorUserModelCBID(
                    momentModel
                )
            );

            let userLinkElement = document.createElement(
                "a"
            );

            userLinkElement.className = "CB_CBView_Moment_userLink_element";

            userLinkElement.href = (
                `/user/${publicProfile.CBUser_publicProfile_prettyUsername}/`
            );

            headerElement.append(
                userLinkElement
            );

            let userFullNameElement = document.createElement(
                "span"
            );

            userFullNameElement.classList.add(
                "CB_CBView_Moment_fullName_element"
            );

            userFullNameElement.textContent = (
                publicProfile.CBUser_publicProfile_fullName
            );

            userLinkElement.append(
                userFullNameElement
            );

            userLinkElement.append(
                " @" + publicProfile.CBUser_publicProfile_prettyUsername
            );

            let timeContainerElement = document.createElement(
                "a"
            );

            timeContainerElement.className = (
                "CB_CBView_Moment_timeContainer_element"
            );

            let momentModelCBID = CBModel.getCBID(
                momentModel
            );

            timeContainerElement.href = `/moment/${momentModelCBID}/`;

            headerElement.append(
                " • "
            );

            headerElement.append(
                timeContainerElement
            );

            let timeElement = Colby.unixTimestampToElement(
                CB_Moment.getCreatedTimestamp(
                    momentModel
                ),
                "",
                "Colby_time_element_style_moment"
            );

            timeContainerElement.append(
                timeElement
            );
        } catch (
            error
        ) {
            CBErrorHandler.report(
                error
            );
        }
    }
    /* populateHeaderElement() */

})();
