/* global
    CB_UI_StringEditor,
    CBAjax,
    CBErrorHandler,
    CBImage,
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


        /* image */

        let addImageButton;
        let imageElement;
        let imageFileInputElement;
        let imageModel;

        {
            let imageContainerElement = document.createElement(
                "div"
            );

            imageContainerElement.className = (
                "CB_CBView_MomentCreator_imageContainer_element"
            );

            imageElement = document.createElement(
                "img"
            );

            imageElement.className = "CB_CBView_MomentCreator_image_element";

            imageContainerElement.append(
                imageElement
            );

            imageFileInputElement = document.createElement("input");
            imageFileInputElement.type = "file";
            imageFileInputElement.style.display = "none";
            imageFileInputElement.accept="image/jpeg, image/png, image/gif";

            element.append(
                imageFileInputElement
            );

            imageFileInputElement.addEventListener(
                "change",
                async function () {
                    try {
                        imageModel = await CBAjax.call(
                            "CBImages",
                            "upload",
                            {},
                            imageFileInputElement.files[0]
                        );

                        let imageURL = CBImage.toURL(
                            imageModel,
                            "rw960"
                        );

                        if (
                            imageURL === ""
                        ) {
                            element.remove(
                                imageContainerElement
                            );

                            imageElement.src = imageURL;
                        } else {
                            imageElement.src = imageURL;

                            element.append(
                                imageContainerElement
                            );
                        }
                    } catch (
                        error
                    ) {
                        CBUIPanel.displayAndReportError(
                            error
                        );
                    }
                }
            );

            addImageButton = CBUIButton.create();

            addImageButton.CBUIButton_setTextContent(
                "Add Image"
            );

            addImageButton.CBUIButton_addClickEventListener(
                function () {
                    imageFileInputElement.click();
                }
            );
        }
        /* image */


        let shareButton = CBUIButton.create();

        shareButton.CBUIButton_setTextContent(
            "Share"
        );

        shareButton.CBUIButton_addClickEventListener(
            function () {
                createMoment();
            }
        );


        /* buttons */
        {
            let buttonBarContainerElement = document.createElement(
                "div"
            );

            buttonBarContainerElement.className = (
                "CB_CBView_MomentCreator_buttonBarContainer"
            );

            let buttonBarElement = document.createElement(
                "div"
            );

            buttonBarElement.className = "CB_CBView_MomentCreator_buttonBar";

            buttonBarElement.append(
                addImageButton.CBUIButton_getElement()
            );

            buttonBarElement.append(
                shareButton.CBUIButton_getElement()
            );

            buttonBarContainerElement.append(
                buttonBarElement
            );

            element.append(
                buttonBarContainerElement
            );
        }
        /* buttons */



        /**
         * @return undefined
         */
        async function
        createMoment(
        ) {
            try {
                shareButton.CBUIButton_setIsDisabled(
                    true
                );

                let response = await CBAjax.call(
                    "CB_Moment",
                    "create",
                    {
                        CB_Moment_create_imageModel_parameter: imageModel,

                        CB_Moment_create_text_parameter: (
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

                imageFileInputElement.value = "";
                imageElement.style.display = "none";

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
                shareButton.CBUIButton_setIsDisabled(
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
