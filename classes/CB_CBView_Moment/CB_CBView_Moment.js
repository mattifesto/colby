/* jshint sub:true */
/* global
    CB_Moment,
    CBAjax,
    CBConvert,
    CBImage,
    CBErrorHandler,
    CBModel,
    CBUIButton,
    CBUIPanel,
    CBUser,
    Colby,

    CBAdministratorsUserGroup_currentUserIsAMember_jsvariable,
    CBUser_currentUserModelCBID_jsvariable,
*/


(function ()
{
    "use strict";

    window.CB_CBView_Moment =
    {
        createStandardMoment:
        CB_CBView_Moment_createStandardMoment,
    };



    /**
     * @return object (CB_CBView_Moment)
     */
    function
    CB_CBView_Moment_create(
    ) // -> object
    {
        let element = document.createElement(
            "div"
        );

        element.className = CBConvert.stringToCleanLine(`

            CB_CBView_Moment_root_element
            CB_CBView_Moment_uninitialized

        `);

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



        /**
         * Initialization is scheduled assuming the caller is going to add the
         * root element to the document. If they store the element and then add
         * it to the document at a much later time, initialization may not
         * occur.
         */

        CB_CBView_Moment_scheduleInitialization();

        return api;
    }
    /* CB_CBView_Moment_create() */



    /**
     * This function creates a delete button that will be shown in the ellipses
     * panel if the current user is allowed to delete a moment.
     *
     * @param object momentModel
     * @param function afterDeletedCallback
     *
     * @return Element
     */
    function
    CB_CBView_Moment_createDeleteButtonElement(
        momentModel,
        afterDeletedCallback
    ) // -> Element
    {
        let deleteButton =
        CBUIButton.create();

        deleteButton.CBUIButton_setTextContent(
            "Delete"
        );

        deleteButton.CBUIButton_addClickEventListener(
            function (
            ) // -> undefined
            {
                CB_CBView_Moment_handleDeleteButtonWasClicked();
            }
        );



        /**
         *
         */
        async function
        CB_CBView_Moment_handleDeleteButtonWasClicked(
        ) // -> Promise -> undefined
        {
            try
            {
                if (
                    deleteButton.CBUIButton_getIsDisabled() !== false
                ) {
                    return;
                }

                deleteButton.CBUIButton_setIsDisabled(
                    true
                );

                let wasConfirmed =
                await
                CBUIPanel.confirmText(
                    "Are you sure you want to delete this moment?"
                );

                if (
                    wasConfirmed
                ) {
                    let momentModelCBID =
                    CBModel.getCBID(
                        momentModel
                    );

                    await CBAjax.call2(
                        "CB_Ajax_Moment_Delete",
                        {
                            CB_Ajax_Moment_Delete_momentModelCBID:
                            momentModelCBID,
                        }
                    );
                }

                deleteButton.CBUIButton_setIsDisabled(
                    false
                );

                if (
                    wasConfirmed
                ) {
                    afterDeletedCallback();
                }
            }

            catch (
                error
            ) {
                CBUIPanel.displayAndReportError(
                    error
                );

                deleteButton.CBUIButton_setIsDisabled(
                    false
                );
            }
        }

        return deleteButton.CBUIButton_getElement();
    }
    // CB_CBView_Moment_createDeleteButtonElement()



    /**
     * @param object momentModel
     *
     * @return Element
     */
    function
    createHeaderElement(
        momentModel
    ) {
        let headerElement =
        document.createElement(
            "div"
        );

        headerElement.className =
        "CB_CBView_Moment_header_element";

        headerElement.append(
            createInformationElement(
                momentModel
            )
        );

        headerElement.append(
            CB_CBView_Moment_createEllipsisElement()
        );

        return headerElement;
    }
    /* createHeaderElement() */



    /**
     * @return element
     */
    function
    CB_CBView_Moment_createEllipsisElement(
    ) // -> Element
    {
        let ellipsisElement =
        document.createElement(
            "div"
        );

        ellipsisElement.className =
        "CB_CBView_Moment_ellipsis_element";

        ellipsisElement.textContent =
        "\u00A0";

        return ellipsisElement;
    }
    // CB_CBView_Moment_createEllipsisElement()



    /**
     * @param object momentModel
     *
     * @return Element
     */
    function
    CB_CBView_Moment_createFooterElement(
        momentModel
    ) // -> Element
    {
        let footerElement =
        document.createElement(
            "div"
        );

        footerElement.className =
        "CB_CBView_Moment_footer_element";



        // moment page link

        let momentModelCBID =
        CBModel.getCBID(
            momentModel
        );

        let momentPageLinkElement =
        document.createElement(
            "a"
        );

        footerElement.append(
            momentPageLinkElement
        );

        momentPageLinkElement.textContent =
        "moment >";

        momentPageLinkElement.title =
        "go to moment page";

        momentPageLinkElement.href =
        `/moment/${momentModelCBID}/`;



        // email

        let sendEmailElement =
        document.createElement(
            "a"
        );

        footerElement.append(
            sendEmailElement
        );

        sendEmailElement.textContent =
        "share";

        sendEmailElement.title =
        "share using email";

        let emailBodyAsURIComponent =
        encodeURIComponent(
            CB_Moment.getText(
                momentModel
            ) +
            "\n\n" +
            document.location.origin +
            `/moment/${momentModelCBID}/`
        );

        sendEmailElement.href =
        `mailto:?subject=Moment&body=${emailBodyAsURIComponent}`;



        // done

        return footerElement;
    }
    // CB_CBView_Moment_createFooterElement()



    /**
     * @param object momentModel
     *
     * @return Element|undefined
     */
    function
    CB_CBView_Moment_createImageElement(
        momentModel
    ) // -> Element|undefined
    {
        let imageModel =
        CB_Moment.getImage(
            momentModel
        );

        if (
            imageModel === undefined
        ) {
            return;
        }

        let momentModelCBID =
        CBModel.getCBID(
            momentModel
        );

        let imageLinkElement =
        document.createElement(
            "a"
        );

        imageLinkElement.className =
        "CB_CBView_Moment_pictureContainer_element";

        imageLinkElement.href =
        `/moment/${momentModelCBID}/`;

        imageLinkElement.style.display =
        "block";

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

        imageLinkElement.append(
            pictureElement
        );

        return imageLinkElement;
    }
    // CB_CBView_Moment_createImageElement()



    /**
     * @param object momentModel
     *
     * @return object
     */
    function
    CB_CBView_Moment_createStandardMoment(
        momentModel
    ) // -> object
    {
        let momentView =
        CB_CBView_Moment_create();

        let momentViewElement =
        momentView.CB_CBView_Moment_getElement();

        momentViewElement.classList.add(
            "CB_CBView_Moment_standard_element"
        );

        momentViewElement.CB_CBView_Moment_getMomentModel =
        function (
        ) // -> object
        {
            return momentModel;
        };

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



        // image

        let imageElement =
        CB_CBView_Moment_createImageElement(
            momentModel
        );

        if (
            imageElement !== undefined
        ) {
            momentView.CB_CBView_Moment_append(
                imageElement
            );
        }



        // footer

        momentView.CB_CBView_Moment_append(
            CB_CBView_Moment_createFooterElement(
                momentModel
            )
        );



        // done

        return momentView;
    }
    /* CB_CBView_Moment_createStandardMoment() */



    /**
     * @param object momentModel
     *
     * @return Element
     */
    function
    createInformationElement(
        momentModel
    ) // Promise -> undefined
    {
        let informationElement =
        document.createElement(
            "div"
        );

        informationElement.className =
        "CB_CBView_Moment_information_element";


        // user link

        let userLinkElement =
        document.createElement(
            "a"
        );

        informationElement.append(
            userLinkElement
        );

        userLinkElement.className =
        "CB_CBView_Moment_userLink_element";


        // user link -> user full name

        let userFullNameElement =
        document.createElement(
            "span"
        );

        userLinkElement.append(
            userFullNameElement
        );

        userFullNameElement.classList.add(
            "CB_CBView_Moment_fullName_element"
        );

        // user link -> user pretty username

        let userPrettyUsernameElement =
        document.createElement(
            "span"
        );

        userLinkElement.append(
            userPrettyUsernameElement
        );

        userPrettyUsernameElement.className =
        "CB_CBView_Moment_prettyUsername_element";

        // separator

        let separatorElement =
        document.createElement(
            "span"
        );

        informationElement.append(
            separatorElement
        );

        separatorElement.textContent =
        " • ";

        // time

        let timeContainerElement =
        document.createElement(
            "a"
        );

        informationElement.append(
            timeContainerElement
        );

        timeContainerElement.className =
        "CB_CBView_Moment_timeContainer_element";


        (async function () {
            try {
                let publicProfile =
                await CBUser.fetchPublicProfileByUserModelCBID(
                    CB_Moment.getAuthorUserModelCBID(
                        momentModel
                    )
                );

                userLinkElement.href =
                `/user/${publicProfile.CBUser_publicProfile_prettyUsername}/`;

                userFullNameElement.textContent =
                publicProfile.CBUser_publicProfile_fullName;

                userPrettyUsernameElement.textContent =
                " @" +
                publicProfile.CBUser_publicProfile_prettyUsername;


                let momentModelCBID =
                CBModel.getCBID(
                    momentModel
                );

                timeContainerElement.href =
                `/moment/${momentModelCBID}/`;

                let timeElement =
                Colby.unixTimestampToElement(
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
        })();

        return informationElement;
    }
    // createInformationElement()



    /**
     * @param object momentModel
     *
     * @return Element
     */
    function
    CB_CBView_Moment_displayEllipsisPanel(
        momentModel
    ) // -> Element
    {
        let panelContentElement =
        document.createElement(
            "div"
        );

        panelContentElement.className =
        "CB_CBView_Moment_panelContent_element";

        let authorUserModelCBID =
        CB_Moment.getAuthorUserModelCBID(
            momentModel
        );

        if (
            authorUserModelCBID === CBUser_currentUserModelCBID_jsvariable ||
            CBAdministratorsUserGroup_currentUserIsAMember_jsvariable
        ) {
            panelContentElement.append(
                CB_CBView_Moment_createDeleteButtonElement(
                    momentModel,
                    function (
                    ) // -> undefined
                    {
                        CBUIPanel.hidePanelWithContentElement(
                            panelContentElement
                        );
                    }
                )
            );
        }

        let closeButton =
        CBUIButton.create();

        panelContentElement.append(
            closeButton.CBUIButton_getElement()
        );

        closeButton.CBUIButton_setTextContent(
            "Close"
        );

        closeButton.CBUIButton_addClickEventListener(
            function (
            ) // -> undefined
            {
                CBUIPanel.hidePanelWithContentElement(
                    panelContentElement
                );
            }
        );

        CBUIPanel.displayElement(
            panelContentElement
        );
    }
    // CB_CBView_Moment_createPanelContentElement()



    /**
     * This function will schedule the initialization of any uninitialized
     * CB_CBView_Moment elements to take place in the near future.
     *
     * @return undefined
     */
    let
    CB_CBView_Moment_scheduleInitialization =
    (function ()
    {
        let timeoutID;



        /**
         * @return undefined
         */
        function
        CB_CBView_Moment_innerExecuteInitialization(
        ) // -> undefined
        {
            let momentViewElements =
            Array.from(
                document.getElementsByClassName(
                    "CB_CBView_Moment_uninitialized"
                )
            );

            momentViewElements.forEach(
                function (
                    momentViewElement
                ) // -> undefined
                {
                    momentViewElement.classList.remove(
                        "CB_CBView_Moment_uninitialized"
                    );

                    /**
                     * @NOTE 2022_03_23
                     *
                     *      If this moment view was created in JavaScript, the
                     *      CB_CBView_Moment_getMomentModel() function will
                     *      already exist on the moment view element.
                     *
                     *      For elements rendered by HTML, the
                     *      data-momentmodelasjson0959d46fd2 attribute will hold
                     *      the moment model and we will create the
                     *      CB_CBView_Moment_getMomentModel() function on the
                     *      moment view element right now.
                     */
                    let potentialMomentModel =
                    momentViewElement.dataset[
                        "momentmodelasjson0959d46fd2"
                    ];

                    if (
                        potentialMomentModel !== undefined
                    ) {
                        let momentModel =
                        JSON.parse(
                            potentialMomentModel
                        );

                        momentViewElement.CB_CBView_Moment_getMomentModel =
                        function (
                        ) // -> object
                        {
                            return momentModel;
                        };

                        momentViewElement.removeAttribute(
                            "data-momentmodelasjson0959d46fd2"
                        );
                    }

                    let ellipsisElement =
                    momentViewElement.getElementsByClassName(
                        "CB_CBView_Moment_ellipsis_element"
                    )[0];

                    ellipsisElement.textContent =
                    "\u22EF";

                    ellipsisElement.addEventListener(
                        "click",
                        function (
                            event
                        )
                        {
                            let momentModel =
                            momentViewElement.CB_CBView_Moment_getMomentModel();

                            CB_CBView_Moment_displayEllipsisPanel(
                                momentModel
                            );

                            event.stopPropagation();
                        }
                    );
                }
            );

            timeoutID = undefined;
        }
        // CB_CBView_Moment_innerExecuteInitialization()



        /**
         * @return undefined
         */
        function
        CB_CBView_Moment_innerScheduleInitialization(
        ) // -> undefined
        {
            if (
                timeoutID !== undefined
            ) {
                clearTimeout(
                    timeoutID
                );
            }

            timeoutID =
            setTimeout(
                function ()
                {
                    CB_CBView_Moment_innerExecuteInitialization();
                },
                50
            );
        }
        // CB_CBView_Moment_scheduleInitialization()



        /**
         * Initialization is scheduled so that any moment views that were
         * rendered on the server side will be initialized.
         */

        CB_CBView_Moment_innerScheduleInitialization();

        return CB_CBView_Moment_innerScheduleInitialization;
    }
    )();
    // CB_CBView_Moment_scheduleInitialization()

}
)();
