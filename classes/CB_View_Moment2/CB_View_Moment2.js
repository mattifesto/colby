/* global
    CB_Moment,
    CB_UI,
    CBImage,
    CBErrorHandler,
    CBModel,
    CBUser,
    Colby,
*/

(function ()
{
    "use strict";



    let CB_View_Moment2 =
    {
        create:
        CB_View_Moment2_create,
    };

    window.CB_View_Moment2 =
    CB_View_Moment2;



    /**
     * @return object
     *
     *      {
     *          CB_View_Moment2_getElement() -> Element
     *          CB_View_Moment2_setMomentModel(<object>)
     *      }
     */
    function
    CB_View_Moment2_create(
    ) // --> object
    {
        let shared_momentModel;

        let shared_rootElement =
        CB_View_Moment2_createRootElement();

        CB_View_Moment2_refresh();




        let api =
        {
            CB_View_Moment2_getElement:
            CB_View_Moment2_getElement,

            CB_View_Moment2_setMomentModel:
            CB_View_Moment2_setMomentModel,
        };



        // -- private functions



        /**
         * @param Element rootElementArgument
         *
         * @return Element
         */
        function
        CB_View_Moment2_createContentElement(
        ) // -> Element
        {
            let contentElement =
            document.createElement(
                "div"
            );

            contentElement.className =
            "CB_View_Moment2_content_element";

            shared_rootElement.append(
                contentElement
            );

            return contentElement;
        }
        // CB_View_Moment2_createContentElement()



        /**
         * @param Element contentElementArgument
         *
         * @return undefined
         */
        function
        CB_View_Moment2_createImageElement(
            contentElementArgument
        ) // -> undefined
        {
            let imageModel =
            CB_Moment.getImage(
                shared_momentModel
            );

            if (
                imageModel ===
                undefined
            ) {
                return;
            }

            let alternativeText =
            CB_Moment.getImageAlternativeText(
                shared_momentModel
            );

            if (
                alternativeText ===
                ""
            ) {
                alternativeText =
                "Image";
            }

            let pictureElement =
            CBImage.createPictureElementWithMaximumDisplayWidthAndHeight(
                imageModel,
                "rh800rw2560",
                1280,
                400,
                alternativeText
            );

            contentElementArgument.append(
                pictureElement
            );
        }
        // CB_View_Moment2_createImageElement()



        /**
         * @return Element
         */
        function
        CB_View_Moment2_createLinkElementForEmail(
        ) // -> Element
        {
            let linkElement =
            document.createElement(
                "a"
            );

            linkElement.textContent =
            "email";

            let momentModelCBID =
            CBModel.getCBID(
                shared_momentModel
            );

            let momentText =
            CB_Moment.getText(
                shared_momentModel
            );

            let emailBodyAsURIComponent =
            encodeURIComponent(
                momentText +
                "\n\n" +
                document.location.origin +
                `/moment/${momentModelCBID}/`
            );

            linkElement.href =
            `mailto:?subject=Moment&body=${emailBodyAsURIComponent}`;

            return linkElement;
        }
        // CB_View_Moment2_createLinkElementForEmail()



        /**
         * @return Element
         */
        function
        CB_View_Moment2_createLinkElementForMore(
        ) // -> Element
        {
            let linkElement =
            document.createElement(
                "a"
            );

            linkElement.textContent =
            "more";

            let momentModelCBID =
            CBModel.getCBID(
                shared_momentModel
            );

            let URL =
            `/moment/${momentModelCBID}/`;

            linkElement.href =
            URL;

            return linkElement;
        }
        // CB_View_Moment2_createLinkElementForMore()



        /**
         * @return Element
         */
        function
        CB_View_Moment2_createLinkElementForUser(
        ) // -> Element
        {
            let linkElement =
            document.createElement(
                "a"
            );

            (async function ()
            {
                try
                {
                    let authorUserModelCBID =
                    CB_Moment.getAuthorUserModelCBID(
                        shared_momentModel
                    );

                    let publicProfile =
                    await CBUser.fetchPublicProfileByUserModelCBID(
                        authorUserModelCBID
                    );

                    let prettyUsername =
                    publicProfile.CBUser_publicProfile_prettyUsername;

                    linkElement.textContent =
                    `@${prettyUsername}`;

                    let URL =
                    `/user/${prettyUsername}/`;

                    linkElement.href =
                    URL;
                }
                // try

                catch (
                    error
                ) {
                    CBErrorHandler.report(
                        error
                    );
                }
                // catch
            }
            )();

            return linkElement;
        }
        // CB_View_Moment2_createLinkElementForUser()



        /**
         * @param Element contentElementArgument
         *
         * @return undefined
         */
        function
        CB_View_Moment2_createLinksElement(
            contentElementArgument
        ) // -> undefined
        {
            let linksElement =
            document.createElement(
                "div"
            );

            linksElement.className =
            "CB_View_Moment2_links_element";

            let arrayOfLinkElements =
            [];

            arrayOfLinkElements.push(
                CB_View_Moment2_createLinkElementForMore()
            );

            arrayOfLinkElements.push(
                CB_View_Moment2_createLinkElementForUser()
            );

            arrayOfLinkElements.push(
                CB_View_Moment2_createLinkElementForEmail()
            );

            arrayOfLinkElements.forEach(
                function (
                    linkElementArgument,
                    linkElementIndexArgument,
                ) {
                    if (
                        linkElementIndexArgument >
                        0
                    ) {
                        linksElement.append(
                            " • "
                        );
                    }

                    linksElement.append(
                        linkElementArgument
                    );
                }
            );

            contentElementArgument.append(
                linksElement
            );
        }
        // CB_View_Moment2_createLinksElement()



        /**
         * @param Element contentElementArgument
         *
         * @return undefined
         */
        function
        CB_View_Moment2_createNameAndDateElement(
            contentElementArgument
        ) // -> undefined
        {
            let nameAndDateElement =
            document.createElement(
                "div"
            );

            nameAndDateElement.className =
            "CB_View_Moment2_nameAndDate_element";

            CB_View_Moment2_createNameElement(
                nameAndDateElement
            );

            let dateElement =
            document.createElement(
                "div"
            );

            nameAndDateElement.append(
                dateElement
            );

            let timeElement =
            Colby.unixTimestampToElement(
                CB_Moment.getCreatedTimestamp(
                    shared_momentModel
                ),
                "",
                "Colby_time_element_style_moment"
            );

            dateElement.append(
                timeElement
            );

            contentElementArgument.append(
                nameAndDateElement
            );
        }
        // CB_View_Moment2_createNameAndDateElement()



        /**
         * @param Element nameAndDateElementArgument
         *
         * @return undefined
         */
        function
        CB_View_Moment2_createNameElement(
            nameAndDateElementArgument
        ) // -> undefined
        {
            let nameElement =
            document.createElement(
                "div"
            );

            nameElement.textContent =
            CB_UI.getNonBreakingSpaceCharacter();

            nameAndDateElementArgument.append(
                nameElement
            );

            (async function ()
            {
                try
                {
                    let authorUserModelCBID =
                    CB_Moment.getAuthorUserModelCBID(
                        shared_momentModel
                    );

                    let publicProfile =
                    await CBUser.fetchPublicProfileByUserModelCBID(
                        authorUserModelCBID
                    );

                    nameElement.textContent =
                    publicProfile.CBUser_publicProfile_fullName;
                }
                // try

                catch (
                    error
                ) {
                    CBErrorHandler.report(
                        error
                    );
                }
                // catch
            }
            )();
        }
        // CB_View_Moment2_createNameElement()



        /**
         * @return Element
         */
        function
        CB_View_Moment2_createRootElement(
        ) // -> Element
        {
            let rootElement =
            document.createElement(
                "div"
            );

            rootElement.className =
            "CB_View_Moment2_root_element";

            return rootElement;
        }
        // CB_View_Moment2_createRootElement()



        /**
         * @param Element contentElementArgument
         *
         * @return undefined
         */
        function
        CB_View_Moment2_createTextElement(
            contentElementArgument
        ) // -> undefined
        {
            let text =
            CB_Moment.getText(
                shared_momentModel
            );

            if (
                text.trim() ===
                ""
            ) {
                return;
            }

            let textElement =
            document.createElement(
                "div"
            );

            textElement.textContent =
            text;

            textElement.className =
            "CB_View_Moment2_text_element";

            contentElementArgument.append(
                textElement
            );
        }
        // CB_View_Moment2_createTextElement()



        /**
         * @return undefined
         */
        function
        CB_View_Moment2_refresh(
        ) // --> undefined
        {
            /**
             * Remove root element content.
             */
            shared_rootElement.textContent =
            "";

            if (
                shared_momentModel ===
                undefined
            ) {
                return;
            }

            let contentElement =
            CB_View_Moment2_createContentElement();

            CB_View_Moment2_createImageElement(
                contentElement
            );

            CB_View_Moment2_createTextElement(
                contentElement
            );

            CB_View_Moment2_createNameAndDateElement(
                contentElement
            );

            CB_View_Moment2_createLinksElement(
                contentElement
            );
        }
        // -- api functions



        /**
         * @return Element
         */
        function
        CB_View_Moment2_getElement(
        ) // --> Element
        {
            return shared_rootElement;
        }
        // CB_View_Moment2_getElement()



        /**
         * @param object momentModelArgument
         *
         * @return undefined
         */
        function
        CB_View_Moment2_setMomentModel(
            momentModelArgument
        ) // -> undefined
        {
            shared_momentModel =
            momentModelArgument;

            CB_View_Moment2_refresh();
        }
        // CB_View_Moment2_setMomentModel()



        // -- done



        return api;
    }
    // CB_View_Moment2_create()

}
)();
