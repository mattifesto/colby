(function () {
    "use strict";

    window.CB_Brick_KeyValue = {
        create: CB_Brick_KeyValue_create,
    };



    /**
     * @return object
     *
     *      {
     *          CB_Brick_KeyValue_getElement: function
     *          CB_Brick_KeyValue_setHasNavigationArrow: function
     *          CB_Brick_KeyValue_setKey: function
     *          CB_Brick_KeyValue_setValue: function
     *      }
     */
    function
    CB_Brick_KeyValue_create(
    ) {
        let element = document.createElement(
            "div"
        );

        element.className = "CB_Brick_KeyValue";

        let keyValueContainerElement = document.createElement(
            "div"
        );

        keyValueContainerElement.className = (
            "CB_Brick_KeyValue_keyValueContainer"
        );

        element.append(
            keyValueContainerElement
        );

        let keyElement = document.createElement(
            "div"
        );

        keyElement.className = "CB_Brick_KeyValue_key";

        keyValueContainerElement.append(
            keyElement
        );

        let valueElement = document.createElement(
            "div"
        );

        valueElement.className = "CB_Brick_KeyValue_value";

        keyValueContainerElement.append(
            valueElement
        );

        let navigationArrowElement = document.createElement(
            "div"
        );

        navigationArrowElement.className = "CB_Brick_KeyValue_navigationArrow";
        navigationArrowElement.textContent = ">";

        element.append(
            navigationArrowElement
        );



        /**
         * @return Element
         */
        function
        CB_Brick_KeyValue_getElement(
        ) {
            return element;
        }
        /* CB_Brick_KeyValue_getElement() */



        /**
         * @param bool hasNavigationArrow
         *
         * @return undefined
         */
        function
        CB_Brick_KeyValue_setHasNavigationArrow(
            hasNavigationArrow
        ) {
            if (
                !!hasNavigationArrow
            ) {
                element.classList.add(
                    "CB_Brick_KeyValue_hasNavigationArrow"
                );
            } else {
                element.classList.remove(
                    "CB_Brick_KeyValue_hasNavigationArrow"
                );
            }
        }
        /* CB_Brick_KeyValue_setHasNavigationArrow() */



        /**
         * @param string newKey
         *
         * @return undefined
         */
        function
        CB_Brick_KeyValue_setKey(
            newKey
        ) {
            keyElement.textContent = newKey;
        }
        /* CB_Brick_KeyValue_setKey() */



        /**
         * @param string newValue
         *
         * @return undefined
         */
        function
        CB_Brick_KeyValue_setValue(
            newValue
        ) {
            valueElement.textContent = newValue;
        }
        /* CB_Brick_KeyValue_setValue() */



        return {
            CB_Brick_KeyValue_getElement,
            CB_Brick_KeyValue_setHasNavigationArrow,
            CB_Brick_KeyValue_setKey,
            CB_Brick_KeyValue_setValue,
        };
    }
    /* CB_Brick_KeyValue_create() */

})();
