(function () {
    "use strict";

    window.CB_Brick_TextContainer = {
        create: CB_Brick_TextContainer_create,
    };



    /**
     * @return object
     *
     *      {
     *          CB_Brick_TextContainer_getOuterElement: function
     *          CB_Brick_TextContainer_getInnerElement: function
     *      }
     */
    function
    CB_Brick_TextContainer_create(
    ) {
        let outerElement = document.createElement(
            "div"
        );

        outerElement.className = "CB_Brick_TextContainer";

        let innerElement = document.createElement(
            "div"
        );

        innerElement.className = "CB_Brick_TextContainer_inner";

        outerElement.append(
            innerElement
        );



        /* -- accessors -- */



        /**
         * @return Element
         */
        function
        CB_Brick_TextContainer_getOuterElement(
        ) {
            return outerElement;
        }
        /* CB_Brick_TextContainer_getOuterElement() */



        /**
         * @return Element
         */
        function
        CB_Brick_TextContainer_getInnerElement(
        ) {
            return innerElement;
        }
        /* CB_Brick_TextContainer_getInnerElement() */



        /* -- return -- */



        return {
            CB_Brick_TextContainer_getOuterElement,
            CB_Brick_TextContainer_getInnerElement,
        };
    }
    /* CB_Brick_TextContainer_create() */

})();
