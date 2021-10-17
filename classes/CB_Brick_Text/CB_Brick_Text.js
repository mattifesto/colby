(function () {
    "use strict";

    window.CB_Brick_Text = {
        create: CB_Brick_Text_create,
    };



    /**
     * @return object
     *
     *      {
     *          CB_Brick_Text_getOuterElement: function
     *          CB_Brick_Text_setText: function
     *      }
     */
    function
    CB_Brick_Text_create() {
        let element = document.createElement(
            "div"
        );

        element.className = "CB_Brick_Text";



        /**
         * @return Element
         */
        function
        CB_Brick_Text_getOuterElement(
        ) {
            return element;
        }
        /* CB_Brick_Text_getOuterElement() */



        /**
         * @param string text
         *
         * @return undefined
         */
        function
        CB_Brick_Text_setText(
            text
        ) {
            let adjustedText = text.trim();

            adjustedText = adjustedText.replace(
                "\r\n",
                "\n"
            );

            adjustedText = adjustedText.replace(
                /\n\n+/g,
                "\n\n"
            );

            element.textContent = adjustedText;
        }
        /* CB_Brick_Text_setText() */



        return {
            CB_Brick_Text_getOuterElement,
            CB_Brick_Text_setText,
        };
    }
    /* CB_Brick_Text_create() */

})();
