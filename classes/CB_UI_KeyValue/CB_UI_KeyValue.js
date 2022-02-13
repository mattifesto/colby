(function () {
    "use strict";

    window.CB_UI_KeyValue = {
        create: CB_UI_KeyValue_create,
    };



    /**
     * @return object
     */
    function
    CB_UI_KeyValue_create(
    ) {
        let element = document.createElement(
            "div"
        );

        element.className = "CB_UI_KeyValue_element";

        let contentElement = document.createElement(
            "div"
        );

        contentElement.className = "CB_UI_KeyValue_content_element";

        element.append(
            contentElement
        );

        let keyElement = document.createElement(
            "div"
        );

        keyElement.className = "CB_UI_KeyValue_key_element";

        contentElement.append(
            keyElement
        );

        let valueElement = document.createElement(
            "div"
        );

        valueElement.className = "CB_UI_KeyValue_value_element";

        contentElement.append(
            valueElement
        );



        /**
         * @return Element
         */
        function
        CB_UI_KeyValue_getElement(
        ) {
            return element;
        }
        /* CB_UI_KeyValue_getElement() */



        /**
         * @param string newKey
         *
         * @return undefined
         */
        function
        CB_UI_KeyValue_setKey(
            newKey
        ) {
            keyElement.textContent = newKey;
        }
        /* CB_UI_KeyValue_setKey() */



        /**
         * @param string newValue
         *
         * @return undefined
         */
        function
        CB_UI_KeyValue_setValue(
            newValue
        ) {
            valueElement.textContent = newValue;
        }
        /* CB_UI_KeyValue_setValue() */



        return {
            CB_UI_KeyValue_getElement,
            CB_UI_KeyValue_setKey,
            CB_UI_KeyValue_setValue,
        };
    }
    /* CB_UI_KeyValue_create() */

})();
