(function () {
    "use strict";

    window.CB_Brick_HorizontalBar10 = {
        create: CB_Brick_HorizontalBar10_create,
    };



    /**
     * @return object
     *
     *      {
     *          CB_Brick_HorizontalBar10_getElement: function
     *      }
     */
    function
    CB_Brick_HorizontalBar10_create(
    ) {
        let element = document.createElement(
            "div"
        );

        element.className = "CB_Brick_HorizontalBar10";



        /* -- accessors -- */



        /**
         * @return Element
         */
        function
        CB_Brick_HorizontalBar10_getElement(
        ) {
            return element;
        }
        /* CB_Brick_HorizontalBar10_getElement() */



        return {
            CB_Brick_HorizontalBar10_getElement,
        };
    }
    /* CB_Brick_HorizontalBar10_create() */

})();
