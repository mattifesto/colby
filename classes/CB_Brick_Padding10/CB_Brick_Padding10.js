(function () {
    "use strict";

    window.CB_Brick_Padding10 = {
        create: CB_Brick_Padding10_create,
    };



    /**
     * @return object
     *
     *      {
     *
     *      }
     */
    function
    CB_Brick_Padding10_create(
    ) {
        let outerElement = document.createElement(
            "div"
        );

        outerElement.className = "CB_Brick_Padding10 CB_Brick_Padding10_outer";

        let innerElement = document.createElement(
            "div"
        );

        innerElement.className = "CB_Brick_Padding10_inner";

        outerElement.append(
            innerElement
        );



        /**
         * @return Element
         */
        function
        CB_Brick_Padding10_getInnerElement(
        ) {
            return innerElement;
        }
        /* CB_Brick_Padding10_getInnerElement() */



        /**
         * @return Element
         */
        function
        CB_Brick_Padding10_getOuterElement(
        ) {
            return outerElement;
        }
        /* CB_Brick_Padding10_getOuterElement() */



        return {
            CB_Brick_Padding10_getInnerElement,
            CB_Brick_Padding10_getOuterElement,
        };
    }
    /* CB_Brick_Padding10_create() */

})();
