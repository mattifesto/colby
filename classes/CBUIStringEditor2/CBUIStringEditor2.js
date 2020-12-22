"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* globals
    CBConvert,
    CBException,
    CBModel,
    CBUIStringEditor,
*/


(function () {

    window.CBUIStringEditor2 = {
        create,
        createObjectPropertyEditorElement,
    };



    /**
     * @return object
     */
    function
    create(
    ) {
        let stringEditor = CBUIStringEditor.create();

        return {
            CBUIStringEditor2_getElement,
            CBUIStringEditor2_getValue,
            CBUIStringEditor2_initializeObjectPropertyEditor,
            CBUIStringEditor2_setChangedEventListener,
            CBUIStringEditor2_setPlaceholderText,
            CBUIStringEditor2_setTitle,
            CBUIStringEditor2_setValue,
        };



        /**
         * @return Element
         */
        function
        CBUIStringEditor2_getElement(
        ) {
            return stringEditor.element;
        }
        /* CBUIStringEditor2_getElement() */



        /**
         * @return string
         */
        function
        CBUIStringEditor2_getValue(
        ) {
            return stringEditor.value;
        }
        /* CBUIStringEditor2_getValue() */



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
         * @param function listener
         *
         * @return undefined
         */
        function
        CBUIStringEditor2_setChangedEventListener(
            listener
        ) {
            stringEditor.changed = listener;
        }
        /* CBUIStringEditor2_setChangedEventListener() */



        /**
         * @param function listener
         *
         * @return undefined
         */
        function
        CBUIStringEditor2_setPlaceholderText(
            value
        ) {
            stringEditor.CBUIStringEditor_setPlaceholderText(
                CBConvert.valueToString(
                    value
                )
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
            stringEditor.title = title;
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
            stringEditor.value = value;
        }
        /* CBUIStringEditor2_setValue() */

    }
    /* create() */



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
