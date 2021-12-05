/* global
    CB_Moment,
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
     *
     * @return CB_CBView_Moment
     */
    function
    createStandardMoment(
        momentModel
    ) {
        let momentView = create();

        momentView.CB_CBView_Moment_getElement().classList.add(
            "CB_CBView_Moment_standard_element"
        );

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
        let publicProfile = await CBUser.fetchPublicProfileByUserModelCBID(
            CB_Moment.getAuthorUserModelCBID(
                momentModel
            )
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

        headerElement.append(
            userFullNameElement
        );

        headerElement.append(
            " @" + publicProfile.CBUser_publicProfile_username
        );

        let timeContainerElement = document.createElement(
            "a"
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
    }
    /* populateHeaderElement() */


})();
