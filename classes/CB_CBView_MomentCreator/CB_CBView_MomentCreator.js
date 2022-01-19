/* global
    CB_UI_StringEditor,
    CBAjax,
    CBErrorHandler,
    CBUIButton,
    CBUIPanel,
    Colby,

    CB_Moment_showMomentEditor_jsvariable,
*/


(function () {
    "use strict";

    window.CB_CBView_MomentCreator = {
        create: CB_CBView_MomentCreator_create,
    };



    Colby.afterDOMContentLoaded(
        function () {
            let elements = Array.from(
                document.getElementsByClassName(
                    "CB_CBView_MomentCreator"
                )
            );

            elements.forEach(
                function (element) {
                    CB_CBView_MomentCreator_initializeElement(
                        element
                    );
                }
            );
        }
    );



    /**
     * @return object
     *
     *      {
     *          CB_CBView_MomentCreator_getElement() -> Element|undefined
     *      }
     */
    function
    CB_CBView_MomentCreator_create(
    ) {
        if (
            CB_Moment_showMomentEditor_jsvariable !== true
        ) {
            return;
        }

        let newMomentCallback;

        let element = document.createElement(
            "div"
        );

        element.className = "CB_CBView_MomentCreator";

        let stringEditor = CB_UI_StringEditor.create();

        stringEditor.CB_UI_StringEditor_setPlaceholderText(
            "Share a Moment"
        );

        let stringEditorElement = stringEditor.CB_UI_StringEditor_getElement();

        stringEditorElement.classList.add(
            "CB_UI_StringEditor_tall"
        );

        element.append(
            stringEditorElement
        );

        let button = CBUIButton.create();

        button.CBUIButton_setTextContent(
            "Share"
        );

        button.CBUIButton_addClickEventListener(
            function () {
                createMoment();
            }
        );

        element.append(
            button.CBUIButton_getElement()
        );



        /**
         * @return undefined
         */
        async function
        createMoment(
        ) {
            try {
                button.CBUIButton_setIsDisabled(
                    true
                );

                let response = await CBAjax.call(
                    "CB_Moment",
                    "create",
                    {
                        CB_Moment_create_text: (
                            stringEditor.CB_UI_StringEditor_getValue()
                        ),
                    }
                );

                if (
                    response.CB_Moment_create_userErrorMessage !== null
                ) {
                    CBUIPanel.displayText2(
                        response.CB_Moment_create_userErrorMessage
                    );

                    return;
                }

                stringEditor.CB_UI_StringEditor_setValue(
                    ""
                );

                if (
                    newMomentCallback !== undefined
                ) {
                    newMomentCallback(
                        response.CB_Moment_create_momentModel
                    );
                }
            } catch (
                error
            ) {
                CBErrorHandler.report(
                    error
                );

                CBUIPanel.displayCBMessage(`

                    An error occured. Site administrators have been notified.

                `);
            } finally {
                button.CBUIButton_setIsDisabled(
                    false
                );
            }
        }
        /* createMoment() */



        /**
         * @return Element
         */
        function
        CB_CBView_MomentCreator_getElement(
        ) {
            return element;
        }
        /* CB_CBView_MomentCreator_getElement() */



        /**
         * @param function|undefined potentialCallback
         *
         * @return undefined
         */
        function
        CB_CBView_MomentCreator_setNewMomentCallback(
            potentialCallback
        ) {
            if (
                potentialCallback !== undefined &&

                typeof potentialCallback !== "function"
            ) {
                throw new Error("potentialCallback");
            }

            newMomentCallback = potentialCallback;
        }
        /* CB_CBView_MomentCreator_setNewMomentCallback() */



        return {
            CB_CBView_MomentCreator_getElement,
            CB_CBView_MomentCreator_setNewMomentCallback,
        };
    }
    /* CB_CBView_MomentCreator_create() */



    /**
     * @param Element element
     *
     * @return undefined
     */
    function
    CB_CBView_MomentCreator_initializeElement(
        element
    ) {
        let momentCreator = CB_CBView_MomentCreator_create();

        let momentCreatorElement = (
            momentCreator.CB_CBView_MomentCreator_getElement()
        );

        if (
            momentCreatorElement !== undefined
        ) {
            element.append(
                momentCreatorElement
            );
        }
    }
    /* CB_CBView_MomentCreator_initializeElement() */

})();
