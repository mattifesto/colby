/* global
    CB_Moment,
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

        element.className = "CB_CBView_Moment";

        let contentElement = document.createElement(
            "div"
        );

        contentElement.className = "CB_CBView_Moment_content";

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
     * @return CB_CBView_Moment
     */
    function
    createStandardMoment(
        momentModel
    ) {
        let momentView = create();

        momentView.CB_CBView_Moment_getElement().classList.add(
            "CB_CBView_Moment_standard"
        );

        let textElement = document.createElement(
            "div"
        );

        textElement.className = "CB_CBView_Moment_text";

        textElement.textContent = CB_Moment.getText(
            momentModel
        );

        momentView.CB_CBView_Moment_append(
            textElement
        );

        return momentView;
    }
    /* createStandardMoment() */

})();
