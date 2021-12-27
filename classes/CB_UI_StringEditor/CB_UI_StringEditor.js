/* globals
    CBConvert,
    CBException,
    CBID,
    CBModel,
    CBUI,
*/


(function () {
    "use strict";

    window.CB_UI_StringEditor = {
        create,
    };



    /**
     * @return object
     */
    function
    create(
    ) {
        let elements = CBUI.createElementTree(
            "CB_UI_StringEditor",
            "CB_UI_StringEditor_container",
            [
                "CB_UI_StringEditor_label",
                "label"
            ]
        );

        let element = elements[0];
        let containerElement = elements[1];
        let labelElement = elements[2];
        let inputElement;

        let currentExternalChangedEventListener;

        let inputElementCBID = CBID.generateRandomCBID();
        labelElement.htmlFor = inputElementCBID;

        CB_UI_StringEditor_setInputType(
            "CB_UI_StringEditor_inputType_textarea"
        );


        /**
         * @TODO 2015_09_24
         *
         *      We have two timeouts because there is a bug in Safari where the
         *      height is not calculated correctly the first time. The first
         *      height is close which is why we keep both calls. Remove the
         *      second timeout once the bug has been fixed.
         */

        window.setTimeout(
            CB_UI_StringEditor_resize,
            0
        );

        window.setTimeout(
            CB_UI_StringEditor_resize,
            1000
        );


        return {
            CB_UI_StringEditor_focus,
            CB_UI_StringEditor_getElement,
            CB_UI_StringEditor_getName,
            CB_UI_StringEditor_getValue,
            CB_UI_StringEditor_initializeObjectPropertyEditor,
            CB_UI_StringEditor_setChangedEventListener,
            CB_UI_StringEditor_setInputType,
            CB_UI_StringEditor_setName,
            CB_UI_StringEditor_setPlaceholderText,
            CB_UI_StringEditor_setTitle,
            CB_UI_StringEditor_setValue,
        };



        /**
         * @return undefined
         */
        function
        CB_UI_StringEditor_focus(
        ) {
            inputElement.focus();
        }



        /**
         * @return Element
         */
        function
        CB_UI_StringEditor_getElement(
        ) {
            return element;
        }
        /* CB_UI_StringEditor_getElement() */



        /**
         * @return string
         */
        function
        CB_UI_StringEditor_getName(
        ) {
            return inputElement.name;
        }
        /* CB_UI_StringEditor_getName() */



        /**
         * @return string
         */
        function
        CB_UI_StringEditor_getValue(
        ) {
            return inputElement.value;
        }
        /* CB_UI_StringEditor_getValue() */



        /**
         * This function is always set as the only "input" event listener on the
         * inputElement.
         *
         * @return undefined
         */
        function
        CB_UI_StringEditor_handleInputEvent(
        ) {
            CB_UI_StringEditor_resize();

            if (
                typeof currentExternalChangedEventListener === "function"
            ) {
                currentExternalChangedEventListener();
            }
        }
        /* CB_UI_StringEditor_handleInputEvent() */



        /**
         * @param object targetObject
         * @param string targetPropertyName
         * @param string title
         * @param function changedEventListener
         *
         * @return void
         */
        function
        CB_UI_StringEditor_initializeObjectPropertyEditor(
            targetObject,
            targetPropertyName,
            title,
            changedEventListener
        ) {
            {
                let value = CBConvert.valueAsObject(
                    targetObject
                );

                if (value === undefined) {
                    throw CBException.withValueRelatedError(
                        Error(
                            "The \"targetObject\" parameter is not an object."
                        ),
                        targetObject,
                        "96982049e6bbdbb3160376f2c98c884ea8f4c6ea"
                    );
                }
            }

            CB_UI_StringEditor_setTitle(
                title
            );

            CB_UI_StringEditor_setValue(
                CBModel.valueToString(
                    targetObject,
                    targetPropertyName
                )
            );

            CB_UI_StringEditor_setChangedEventListener(
                function () {
                    targetObject[targetPropertyName] = (
                        CB_UI_StringEditor_getValue()
                    );

                    changedEventListener();
                }
            );
        }
        /* CB_UI_StringEditor_initializeObjectPropertyEditor() */



        /**
         * @return undefined
         */
        function CB_UI_StringEditor_resize() {
            inputElement.style.height = "0";
            inputElement.style.height = inputElement.scrollHeight + "px";
        }
        /* CB_UI_StringEditor_resize() */



        /**
         * @param function listener
         *
         * @return undefined
         */
        function
        CB_UI_StringEditor_setChangedEventListener(
            changedEventListener
        ) {
            currentExternalChangedEventListener = changedEventListener;
        }
        /* CB_UI_StringEditor_setChangedEventListener() */



        /**
         * @param string inputType
         *
         * @return undefined
         */
        function
        CB_UI_StringEditor_setInputType(
            inputType
        ) {
            let newInputElement;

            switch (inputType) {
                case "CB_UI_StringEditor_inputType_textarea":

                    newInputElement = CBUI.createElement(
                        "CB_UI_StringEditor_input",
                        "textarea"
                    );

                    break;

                case "CB_UI_StringEditor_inputType_text":

                    newInputElement = CBUI.createElement(
                        "CB_UI_StringEditor_input",
                        "input"
                    );

                    newInputElement.type = "text";

                    break;

                case "CB_UI_StringEditor_inputType_password":

                    newInputElement = CBUI.createElement(
                        "CB_UI_StringEditor_input",
                        "input"
                    );

                    newInputElement.type = "password";

                    break;

                default:

                    throw CBException.withValueRelatedError(
                        Error(
                            CBConvert.stringToCleanLine(`

                                The input type "${inputType}" is not supported
                                by CB_UI_StringEditor_setInputType().

                            `)
                        ),
                        inputType,
                        "dc24edbf4a8e45cdcf009b9b1e74de09a7728888"
                    );
            }

            if (inputElement !== undefined) {
                newInputElement.value = inputElement.value;
                newInputElement.placeholder = inputElement.placeholder;

                inputElement.id = "";

                inputElement.removeEventListener(
                    "input",
                    CB_UI_StringEditor_handleInputEvent
                );

                inputElement.replaceWith(
                    newInputElement
                );
            } else {
                containerElement.appendChild(
                    newInputElement
                );
            }

            newInputElement.id = inputElementCBID;

            newInputElement.addEventListener(
                "input",
                CB_UI_StringEditor_handleInputEvent
            );

            inputElement = newInputElement;
        }
        /* CB_UI_StringEditor_setInputType() */



        /**
         * @param string value
         *
         * @return undefined
         */
        function
        CB_UI_StringEditor_setName(
            value
        ) {
            inputElement.name = CBConvert.valueToString(
                value
            );
        }
        /* CB_UI_StringEditor_setName() */



        /**
         * @param function listener
         *
         * @return undefined
         */
        function
        CB_UI_StringEditor_setPlaceholderText(
            value
        ) {
            inputElement.placeholder = CBConvert.valueToString(
                value
            );
        }
        /* CB_UI_StringEditor_setPlaceholderText() */



        /**
         * @param string title
         *
         * @return undefined
         */
        function
        CB_UI_StringEditor_setTitle(
            title
        ) {
            labelElement.textContent = CBConvert.valueToString(
                title
            );
        }
        /* CB_UI_StringEditor_setTitle() */



        /**
         * @param string value
         *
         * @return undefined
         */
        function
        CB_UI_StringEditor_setValue(
            value
        ) {
            inputElement.value = value;

            CB_UI_StringEditor_resize();
        }
        /* CB_UI_StringEditor_setValue() */

    }
    /* create() */

})();
