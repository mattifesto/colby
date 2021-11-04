(function () {
    "use strict";

    window.CB_Brick_Button = {
        create: CB_Brick_Button_create,
    };



    /**
     * @return object
     *
     *      {
     *          CB_Brick_Button_setClickedCallback: function
     *          CB_Brick_Button_setIsDisabled: function
     *          CB_Brick_Button_getElement: function
     *          CB_Brick_Button_setTextContent: function
     *      }
     */
    function
    CB_Brick_Button_create(
    ) {
        let clickedCallback;
        let isDisabled = false;

        let outerElement = document.createElement(
            "div"
        );

        outerElement.className = "CB_Brick_Button_outer";

        let innerElement = document.createElement(
            "div"
        );

        innerElement.className = "CB_Brick_Button_inner";

        innerElement.addEventListener(
            "click",
            function () {
                if (isDisabled === false) {
                    CB_Brick_Button_handleClick();
                }
            }
        );

        outerElement.append(
            innerElement
        );



        /* -- accessors -- */



        /**
         * @return bool
         */
        function
        CB_Brick_Button_getIsDisabled(
        ) {
            return outerElement.classList.contains(
                "CB_Brick_Button_isDisabled"
            );
        }
        /* CB_Brick_Button_getIsDisabled() */



        /**
         * @param bool newIsDisabled
         *
         * @return undefined
         */
        function
        CB_Brick_Button_setIsDisabled(
            newIsDisabled
        ) {
            isDisabled = !!newIsDisabled;

            if (
                isDisabled
            ) {
                outerElement.classList.add(
                    "CB_Brick_Button_isDisabled"
                );
            } else {
                outerElement.classList.remove(
                    "CB_Brick_Button_isDisabled"
                );
            }
        }
        /* CB_Brick_Button_setIsDisabled() */



        /**
         * @return Element
         */
        function
        CB_Brick_Button_getElement(
        ) {
            return outerElement;
        }
        /* CB_Brick_Button_getElement() */



        /**
         * @param string newTextContent
         *
         * @return undefined
         */
        function
        CB_Brick_Button_setTextContent(
            newTextContent
        ) {
            innerElement.textContent = newTextContent;
        }
        /* CB_Brick_Button_setTextContent() */



        /**
         * @param function newClickedCallback
         *
         * @return undefined
         */
        function
        CB_Brick_Button_setClickedCallback(
            newClickedCallback
        ) {
            if (
                typeof newClickedCallback === "function"
            ) {
                clickedCallback = newClickedCallback;
            } else {
                throw new Error(
                    "The newClickedCallback argument is not a valid callback."
                );
            }
        }
        /* CB_Brick_Button_setClickedCallback() */



        /* -- functions -- */



        /**
         * @return undefined
         */
        function
        CB_Brick_Button_handleClick(
        ) {
            if (
                isDisabled !== true &&
                clickedCallback !== undefined
            ) {
                clickedCallback();
            }
        }
        /* CB_Brick_Button_handleClick() */



        return {
            CB_Brick_Button_setClickedCallback,
            CB_Brick_Button_getIsDisabled,
            CB_Brick_Button_setIsDisabled,
            CB_Brick_Button_getElement,
            CB_Brick_Button_setTextContent,
        };
    }
    /* CB_Brick_Button_create() */

})();
