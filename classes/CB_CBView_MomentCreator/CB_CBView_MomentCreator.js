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


(function ()
{
    "use strict";

    let CB_CBView_MomentCreator =
    {
        create: CB_CBView_MomentCreator_create,
    };

    window.CB_CBView_MomentCreator =
    CB_CBView_MomentCreator;



    Colby.afterDOMContentLoaded(
        function (
        ) // -> undefined
        {
            let elements =
            Array.from(
                document.getElementsByClassName(
                    "CB_CBView_MomentCreator"
                )
            );

            elements.forEach(
                function (
                    element
                ) // -> undefined
                {
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
    ) // -> object
    {
        if (
            CB_Moment_showMomentEditor_jsvariable !==
            true
        ) {
            return;
        }

        let newMomentCallback;

        let momentCreatorRootElement =
        document.createElement(
            "div"
        );

        momentCreatorRootElement.className =
        "CB_CBView_MomentCreator";



        // moment text editor

        let momentTextEditor =
        CB_UI_StringEditor.create();

        momentTextEditor.CB_UI_StringEditor_setPlaceholderText(
            "Share a Moment"
        );

        let momentTextEditorElement =
        momentTextEditor.CB_UI_StringEditor_getElement();

        momentTextEditorElement.classList.add(
            "CB_UI_StringEditor_tall"
        );

        momentCreatorRootElement.append(
            momentTextEditorElement
        );



        // image container element

        let imageContainerElement =
        document.createElement(
            "div"
        );

        imageContainerElement.className =
        "CB_CBView_MomentCreator_imageContainer_element";

        let imageElement =
        document.createElement(
            "img"
        );

        imageElement.className =
        "CB_CBView_MomentCreator_image_element";

        imageContainerElement.append(
            imageElement
        );



        /* image */

        let imageFileInputElement;
        let imageModel;

        imageFileInputElement =
        document.createElement(
            "input"
        );

        imageFileInputElement.type =
        "file";

        imageFileInputElement.style.display =
        "none";

        imageFileInputElement.accept =
        "image/jpeg, image/png, image/gif";

        momentCreatorRootElement.append(
            imageFileInputElement
        );

        imageFileInputElement.addEventListener(
            "change",
            async function(
            ) // -> undefined
            {
                try
                {
                    let imageURL =
                    "";

                    if (
                        imageFileInputElement.files.length >
                        0
                    ) {
                        let imageFile =
                        imageFileInputElement.files[0];

                        imageModel =
                        await CBAjax.call(
                            "CBImages",
                            "upload",
                            {},
                            imageFile
                        );

                        imageURL =
                        CBImage.toURL(
                            imageModel,
                            "rw960"
                        );
                    }

                    imageElement.src =
                    imageURL;

                    if (
                        imageURL ===
                        ""
                    ) {
                        momentCreatorRootElement.removeChild(
                            imageContainerElement
                        );
                    }

                    else
                    {
                        momentCreatorRootElement.append(
                            imageContainerElement
                        );
                    }
                }

                catch (
                    error
                ) {
                    CBUIPanel.displayAndReportError(
                        error
                    );
                }
            }
        );



        // add image button

        let addImageButton =
        CBUIButton.create();

        addImageButton.CBUIButton_setTextContent(
            "Add Image"
        );

        addImageButton.CBUIButton_addClickEventListener(
            function (
            ) // -> undefined
            {
                imageFileInputElement.click();
            }
        );



        // share button

        let shareButton =
        CBUIButton.create();

        shareButton.CBUIButton_setTextContent(
            "Share"
        );

        shareButton.CBUIButton_addClickEventListener(
            function (
            ) // -> undefined
            {
                createMoment();
            }
        );


        /* buttons */
        {
            let buttonBarContainerElement =
            document.createElement(
                "div"
            );

            buttonBarContainerElement.className =
            "CB_CBView_MomentCreator_buttonBarContainer";

            let buttonBarElement =
            document.createElement(
                "div"
            );

            buttonBarElement.className =
            "CB_CBView_MomentCreator_buttonBar";

            buttonBarElement.append(
                addImageButton.CBUIButton_getElement()
            );

            buttonBarElement.append(
                shareButton.CBUIButton_getElement()
            );

            buttonBarContainerElement.append(
                buttonBarElement
            );

            momentCreatorRootElement.append(
                buttonBarContainerElement
            );
        }
        /* buttons */



        /**
         * @return undefined
         */
        async function
        createMoment(
        ) // -> undefined
        {
            try
            {
                shareButton.CBUIButton_setIsDisabled(
                    true
                );

                let executorArguments =
                {
                    CB_Moment_create_imageModel_parameter:
                    imageModel,

                    CB_Moment_create_text_parameter:
                    momentTextEditor.CB_UI_StringEditor_getValue(),
                };

                let response =
                await CBAjax.call(
                    "CB_Moment",
                    "create",
                    executorArguments
                );

                if (
                    response.CB_Moment_create_userErrorMessage !==
                    null
                ) {
                    CBUIPanel.displayText2(
                        response.CB_Moment_create_userErrorMessage
                    );

                    return;
                }

                imageFileInputElement.value =
                "";

                imageModel =
                undefined;

                let imageContainerElementIsVisible =
                momentCreatorRootElement.contains(
                    imageContainerElement
                );

                if (
                    imageContainerElementIsVisible
                ) {
                    momentCreatorRootElement.removeChild(
                        imageContainerElement
                    );
                }

                momentTextEditor.CB_UI_StringEditor_setValue(
                    ""
                );

                if (
                    newMomentCallback !== undefined
                ) {
                    newMomentCallback(
                        response.CB_Moment_create_momentModel
                    );
                }
            }

            catch (
                error
            ) {
                CBErrorHandler.report(
                    error
                );

                CBUIPanel.displayCBMessage(`

                    An error occured. Site administrators have been notified.

                `);
            }

            finally
            {
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
        ) // -> Element
        {
            return momentCreatorRootElement;
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
        ) // -> undefined
        {
            if (
                potentialCallback !==
                undefined &&
                typeof potentialCallback !==
                "function"
            ) {
                throw new Error(
                    "potentialCallback"
                );
            }

            newMomentCallback =
            potentialCallback;
        }
        /* CB_CBView_MomentCreator_setNewMomentCallback() */



        let api =
        {
            CB_CBView_MomentCreator_getElement,
            CB_CBView_MomentCreator_setNewMomentCallback,
        };

        return api;
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
    ) // -> undefined
    {
        let momentCreator =
        CB_CBView_MomentCreator_create();

        let momentCreatorElement =
        momentCreator.CB_CBView_MomentCreator_getElement();

        if (
            momentCreatorElement !==
            undefined
        ) {
            element.append(
                momentCreatorElement
            );
        }
    }
    /* CB_CBView_MomentCreator_initializeElement() */

}
)();
