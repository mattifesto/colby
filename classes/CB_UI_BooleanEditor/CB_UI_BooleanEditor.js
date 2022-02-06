/* global
    CBMessageMarkup,
    CBUIBooleanSwitchPart,
*/


(function () {
    "use strict";

    window.CB_UI_BooleanEditor = {
        create: CB_UI_BooleanEditor_create,
    };



    function
    CB_UI_BooleanEditor_create(
    ) {
        let rootElement = document.createElement(
            "div"
        );

        rootElement.className = "CB_UI_BooleanEditor_root_element";


        let contentElement = document.createElement(
            "div"
        );

        contentElement.className = "CB_UI_BooleanEditor_content_element";

        rootElement.append(
            contentElement
        );


        let textContainerElement = document.createElement(
            "div"
        );

        textContainerElement.className = (
            "CB_UI_BooleanEditor_textContainer_element"
        );

        contentElement.append(
            textContainerElement
        );


        let titleElement = document.createElement(
            "div"
        );

        titleElement.className = "CB_UI_BooleanEditor_title_element";

        textContainerElement.append(
            titleElement
        );



        let descriptionElement = document.createElement(
            "div"
        );

        descriptionElement.className = (
            "CB_UI_BooleanEditor_description_element"
        );

        textContainerElement.append(
            descriptionElement
        );



        let switchPart = CBUIBooleanSwitchPart.create();

        contentElement.appendChild(
            switchPart.element
        );



        /**
         * @param string description_cbmessage
         *
         * @return undefined
         */
        function
        CB_UI_BooleanEditor_setDescription(
            description_cbmessage
        ) {
            descriptionElement.innerHTML = CBMessageMarkup.messageToHTML(
                description_cbmessage
            );
        }
        /* CB_UI_BooleanEditor_setDescription() */



        /**
         * @return Element
         */
        function
        CB_UI_BooleanEditor_getElement(
        ) {
            return rootElement;
        }
        /* CB_UI_BooleanEditor_getElement() */



        /**
         * @param string title_text
         *
         * @return undefined
         */
        function
        CB_UI_BooleanEditor_setTitle(
            title_text
        ) {
            titleElement.textContent = title_text;
        }
        /* CB_UI_BooleanEditor_setTitle() */



        /**
         * @return bool
         */
        function
        CB_UI_BooleanEditor_getValue(
        ) {
            return switchPart.value;
        }
        /* CB_UI_BooleanEditor_getValue() */



        /**
         * @param bool newBooleanValue
         *
         * @return undefined
         */
        function
        CB_UI_BooleanEditor_setValue(
            newBooleanValue
        ) {
            switchPart.value = !!newBooleanValue;
        }
        /* CB_UI_BooleanEditor_setValue() */



        return {
            CB_UI_BooleanEditor_setDescription,
            CB_UI_BooleanEditor_getElement,
            CB_UI_BooleanEditor_setTitle,
            CB_UI_BooleanEditor_getValue,
            CB_UI_BooleanEditor_setValue,
        };
    }
    /* CB_UI_BooleanEditor_create() */

})();
