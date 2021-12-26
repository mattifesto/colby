/* global
    CB_CBView_Moment,
    CBAjax,
    CBErrorHandler,
    CBUIButton,
    CBUIStringEditor2,
    Colby,
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
        let newMomentCallback;

        let element = document.createElement(
            "div"
        );

        element.className = "CB_CBView_MomentCreator2";

        let moment = CB_CBView_Moment.create();

        let momentElement = moment.CB_CBView_Moment_getElement();

        momentElement.classList.add(
            "CB_CBView_Moment_standard_element"
        );

        element.append(
            momentElement
        );

        let stringEditor = CBUIStringEditor2.create();

        stringEditor.CBUIStringEditor2_setPlaceholderText(
            "Share a Moment"
        );

        let stringEditorElement = stringEditor.CBUIStringEditor2_getElement();

        stringEditorElement.classList.add(
            "CBUIStringEditor2_tall"
        );

        moment.CB_CBView_Moment_append(
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
                let response = await CBAjax.call(
                    "CB_Moment",
                    "create",
                    {
                        CB_Moment_create_text: (
                            stringEditor.CBUIStringEditor2_getValue()
                        ),
                    }
                );

                if (
                    response.CB_Moment_create_userErrorMessage !== null
                ) {
                    window.alert(
                        response.CB_Moment_create_userErrorMessage
                    );

                    return;
                }

                stringEditor.CBUIStringEditor2_setValue("");

                if (
                    newMomentCallback !== undefined
                ) {
                    newMomentCallback(
                        response.CB_Moment_create_momentModel
                    );
                }
            } catch (error) {
                CBErrorHandler.report(
                    error
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
