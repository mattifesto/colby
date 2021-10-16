/* jshint esversion: 6 */
/* globals
    CBConvert,
    CBException,
    CBID,
    CBModel,
    CBUI,
*/


(function () {
    "use strict";

    window.CBUIStringEditor2 = {
        create,
        createDollarsInCentsPropertyEditorElement,
        createObjectPropertyEditorElement,
    };



    /**
     * @return object
     */
    function
    create(
    ) {
        let elements = CBUI.createElementTree(
            "CBUIStringEditor2",
            "CBUIStringEditor2_container",
            [
                "CBUIStringEditor2_label",
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

        CBUIStringEditor2_setInputType(
            "CBUIStringEditor2_inputType_textarea"
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
            CBUIStringEditor2_resize,
            0
        );

        window.setTimeout(
            CBUIStringEditor2_resize,
            1000
        );


        return {
            CBUIStringEditor2_focus,
            CBUIStringEditor2_getElement,
            CBUIStringEditor2_getName,
            CBUIStringEditor2_getValue,
            CBUIStringEditor2_initializeObjectPropertyEditor,
            CBUIStringEditor2_setChangedEventListener,
            CBUIStringEditor2_setHasOutline,
            CBUIStringEditor2_setInputType,
            CBUIStringEditor2_setName,
            CBUIStringEditor2_setPlaceholderText,
            CBUIStringEditor2_setTitle,
            CBUIStringEditor2_setValue,
        };



        /**
         * @return undefined
         */
        function
        CBUIStringEditor2_focus(
        ) {
            inputElement.focus();
        }



        /**
         * @return Element
         */
        function
        CBUIStringEditor2_getElement(
        ) {
            return element;
        }
        /* CBUIStringEditor2_getElement() */



        /**
         * @return string
         */
        function
        CBUIStringEditor2_getName(
        ) {
            return inputElement.name;
        }
        /* CBUIStringEditor2_getName() */



        /**
         * @return string
         */
        function
        CBUIStringEditor2_getValue(
        ) {
            return inputElement.value;
        }
        /* CBUIStringEditor2_getValue() */



        /**
         * This function is always set as the only "input" event listener on the
         * inputElement.
         *
         * @return undefined
         */
        function
        CBUIStringEditor2_handleInputEvent(
        ) {
            CBUIStringEditor2_resize();

            if (typeof currentExternalChangedEventListener === "function") {
                currentExternalChangedEventListener();
            }
        }
        /* CBUIStringEditor2_handleInputEvent() */



        /**
         * @param object targetObject
         * @param string targetPropertyName
         * @param string title
         * @param function changedEventListener
         *
         * @return void
         */
        function
        CBUIStringEditor2_initializeObjectPropertyEditor(
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

            CBUIStringEditor2_setTitle(
                title
            );

            CBUIStringEditor2_setValue(
                CBModel.valueToString(
                    targetObject,
                    targetPropertyName
                )
            );

            CBUIStringEditor2_setChangedEventListener(
                function () {
                    targetObject[targetPropertyName] = (
                        CBUIStringEditor2_getValue()
                    );

                    changedEventListener();
                }
            );
        }
        /* CBUIStringEditor2_initializeObjectPropertyEditor() */



        /**
         * @return undefined
         */
        function CBUIStringEditor2_resize() {
            inputElement.style.height = "0";
            inputElement.style.height = inputElement.scrollHeight + "px";
        }
        /* CBUIStringEditor2_resize() */



        /**
         * @param function listener
         *
         * @return undefined
         */
        function
        CBUIStringEditor2_setChangedEventListener(
            changedEventListener
        ) {
            currentExternalChangedEventListener = changedEventListener;
        }
        /* CBUIStringEditor2_setChangedEventListener() */



        /**
         * @param string inputType
         *
         * @return undefined
         */
        function
        CBUIStringEditor2_setInputType(
            inputType
        ) {
            let newInputElement;

            switch (inputType) {
                case "CBUIStringEditor2_inputType_textarea":

                    newInputElement = CBUI.createElement(
                        "CBUIStringEditor2_input",
                        "textarea"
                    );

                    break;

                case "CBUIStringEditor2_inputType_text":

                    newInputElement = CBUI.createElement(
                        "CBUIStringEditor2_input",
                        "input"
                    );

                    newInputElement.type = "text";

                    break;

                case "CBUIStringEditor2_inputType_password":

                    newInputElement = CBUI.createElement(
                        "CBUIStringEditor2_input",
                        "input"
                    );

                    newInputElement.type = "password";

                    break;

                default:

                    throw CBException.withValueRelatedError(
                        Error(
                            CBConvert.stringToCleanLine(`

                                The input type "${inputType}" is not supported
                                by CBUIStringEditor2_setInputType().

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
                    CBUIStringEditor2_handleInputEvent
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
                CBUIStringEditor2_handleInputEvent
            );

            inputElement = newInputElement;
        }
        /* CBUIStringEditor2_setInputType() */



        /**
         * @param bool value
         *
         * @return undefined
         */
        function
        CBUIStringEditor2_setHasOutline(
            value
        ) {
            if (!!value) {
                element.classList.add(
                    "CBUIStringEditor2_hasOutline"
                );
            } else {
                element.classList.remove(
                    "CBUIStringEditor2_hasOutline"
                );
            }
        }
        /* CBUIStringEditor2_setHasOutline() */



        /**
         * @param string value
         *
         * @return undefined
         */
        function
        CBUIStringEditor2_setName(
            value
        ) {
            inputElement.name = CBConvert.valueToString(
                value
            );
        }
        /* CBUIStringEditor2_setName() */



        /**
         * @param function listener
         *
         * @return undefined
         */
        function
        CBUIStringEditor2_setPlaceholderText(
            value
        ) {
            inputElement.placeholder = CBConvert.valueToString(
                value
            );
        }
        /* CBUIStringEditor2_setPlaceholderText() */



        /**
         * @param string title
         *
         * @return undefined
         */
        function
        CBUIStringEditor2_setTitle(
            title
        ) {
            labelElement.textContent = CBConvert.valueToString(
                title
            );
        }
        /* CBUIStringEditor2_setTitle() */



        /**
         * @param string value
         *
         * @return undefined
         */
        function
        CBUIStringEditor2_setValue(
            value
        ) {
            inputElement.value = value;

            CBUIStringEditor2_resize();
        }
        /* CBUIStringEditor2_setValue() */

    }
    /* create() */



    /**
     * @param object targetObject
     * @param string targetPropertyName
     * @param string title
     * @param function changedEventListener
     *
     * @return Element
     */
    function
    createDollarsInCentsPropertyEditorElement(
        targetObject,
        targetPropertyName,
        title,
        changedEventListener
    ) {
        let previousPropertyValue = CBModel.valueAsInt(
            targetObject,
            targetPropertyName
        );

        if (
            previousPropertyValue === undefined ||
            previousPropertyValue < 0
        ) {
            previousPropertyValue = 0;
        }

        let stringEditor = create();

        stringEditor.CBUIStringEditor2_setTitle(
            title
        );

        stringEditor.CBUIStringEditor2_setValue(
            CBConvert.centsToDollars(
                previousPropertyValue
            )
        );

        let stringEditorElement = stringEditor.CBUIStringEditor2_getElement();

        stringEditor.CBUIStringEditor2_setChangedEventListener(
            function () {
                let newPropertyValue = 0;
                let stringValueIsValid = true;

                let stringValue = (
                    stringEditor.CBUIStringEditor2_getValue().trim()
                );

                if (stringValue !== "") {
                    newPropertyValue = CBConvert.dollarsAsCents(
                        stringValue
                    );

                    if (newPropertyValue === undefined) {
                        stringValueIsValid = false;
                    }

                }

                if (stringValueIsValid) {
                    stringEditorElement.classList.remove(
                        "CBUIStringEditor2_error"
                    );

                    if (newPropertyValue !== previousPropertyValue) {
                        previousPropertyValue = newPropertyValue;
                        targetObject[targetPropertyName] = newPropertyValue;

                        changedEventListener();
                    }
                } else {
                    stringEditorElement.classList.add(
                        "CBUIStringEditor2_error"
                    );
                }
            }
        );

        return stringEditorElement;
    }
    /* createDollarsInCentsPropertyEditorElement() */



    /**
     * This function can be useful to reduce the amount of code needed when you
     * need an object property editor and don't need access to the
     * CBUIStringEditor2 object.
     *
     * @param object targetObject
     * @param string targetPropertyName
     * @param string title
     * @param function changedEventListener
     *
     * @return Element
     */
    function
    createObjectPropertyEditorElement(
        targetObject,
        targetPropertyName,
        title,
        changedEventListener
    ) {
        let stringEditor = create();

        stringEditor.CBUIStringEditor2_initializeObjectPropertyEditor(
            targetObject,
            targetPropertyName,
            title,
            changedEventListener
        );

        return stringEditor.CBUIStringEditor2_getElement();
    }
    /* createObjectPropertyEditorElement() */

})();
