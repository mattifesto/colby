/* global
    CBConvert,
    CBImage,
    CBUIButton,
*/

/**
 * This class provides the user interface for selecting and removing images.
 * This class is agnostic as to what happens after an image is selected or
 * removed.
 */
(function ()
{
    "use strict";



    let CB_UI_ImageChooser =
    {
        create:
        CB_UI_ImageChooser_create,
    };

    window.CB_UI_ImageChooser =
    CB_UI_ImageChooser;



    /**
     * @return object
     */
    function
    CB_UI_ImageChooser_create(
    ) // -> object
    {
        let imageWasChosenCallback;
        let imageWasRemovedCallback;
        let currentImageModel;

        let rootElement =
        document.createElement(
            "div"
        );

        rootElement.className =
        "CB_UI_ImageChooser_root_element";



        let contentElement =
        document.createElement(
            "div"
        );

        contentElement.className =
        "CB_UI_ImageChooser_content_element";

        rootElement.append(
            contentElement
        );



        let inputElement =
        CB_UI_ImageChooser_createImageFileInputElement();

        contentElement.append(
            inputElement
        );



        let titleElement =
        document.createElement(
            "div"
        );

        titleElement.className =
        "CB_UI_ImageChooser_title_element";

        contentElement.append(
            titleElement
        );



        let imageContainerElement =
        document.createElement(
            "div"
        );

        imageContainerElement.className =
        "CB_UI_ImageChooser_imageContainer_element";

        let imgElement =
        document.createElement(
            "img"
        );

        imgElement.style.display =
        "none";

        imageContainerElement.append(
            imgElement
        );

        contentElement.appendChild(
            imageContainerElement
        );



        let buttonContainerElement =
        document.createElement(
            "div"
        );

        buttonContainerElement.className =
        "CB_UI_ImageChooser_buttonContainer_element";

        contentElement.appendChild(
            buttonContainerElement
        );



        let chooseButton =
        CBUIButton.create();

        chooseButton.CBUIButton_setTextContent(
            "Choose"
        );

        buttonContainerElement.append(
            chooseButton.CBUIButton_getElement()
        );

        chooseButton.CBUIButton_addClickEventListener(
            function () {
                inputElement.click();
            }
        );



        inputElement.addEventListener(
            "change",
            function () {
                if (
                    typeof imageWasChosenCallback === "function"
                ) {
                    imageWasChosenCallback();
                }

                inputElement.value = null;
            }
        );



        // remove button

        let removeButton =
        CBUIButton.create();

        removeButton.CBUIButton_setTextContent(
            "Remove"
        );

        removeButton.CBUIButton_addClickEventListener(
            function () {
                if (
                    imageWasRemovedCallback !==
                    undefined
                ) {
                    imageWasRemovedCallback();
                }
            }
        );



        /**
         * @return Element
         */
        function
        CB_UI_ImageChooser_getElement(
        ) // -> Element
        {
            return rootElement;
        }
        //CB_UI_ImageChooser_getElement()



        /**
         * @param object|undefined newImageModelArgument
         *
         *      Pass undefined to remove the image.
         *
         * @return undefined
         */
        function
        CB_UI_ImageChooser_setImage(
            newImageModelArgument
        ) // -> undefined
        {
            currentImageModel =
            CBConvert.valueAsModel(
                newImageModelArgument,
                "CBImage"
            );

            if (
                currentImageModel !==
                undefined
            ) {
                imageContainerElement.textContent =
                "";

                imageContainerElement.append(
                    CBImage.createPictureElementWithMaximumDisplayWidthAndHeight(
                        currentImageModel,
                        "rw960",
                        480,
                        480
                    )
                );

                removeButton.CBUIButton_setIsDisabled(
                    false
                );
            }

            else
            {
                imageContainerElement.textContent =
                "";

                removeButton.CBUIButton_setIsDisabled(
                    true
                );
            }
        }
        // CB_UI_ImageChooser_setImage()



        /**
         * @return File|undefined
         */
        function
        CB_UI_ImageChooser_getImageFile(
        ) // -> File|undefined
        {
            if (
                inputElement.files.length > 0
            ) {
                return inputElement.files[0];
            } else {
                return undefined;
            }
        }
        // CB_UI_ImageChooser_getImageFile()



        /**
         * @param function newImageWasChosenCallack
         *
         * @return undefined
         */
        function
        CB_UI_ImageChooser_setImageWasChosenCallback(
            newImageWasChosenCallack
        ) // -> undefined
        {
            imageWasChosenCallback =
            newImageWasChosenCallack;
        }
        // CB_UI_ImageChooser_setImageWasChosenCallback()



        /**
         * @param function newImageWasChosenCallack
         *
         * @return undefined
         */
        function
        CB_UI_ImageChooser_setImageWasRemovedCallback(
            newImageWasRemovedCallack
        ) // -> undefined
        {
            if (
                typeof newImageWasRemovedCallack ===
                "function"
            ) {
                imageWasRemovedCallback =
                newImageWasRemovedCallack;

                buttonContainerElement.append(
                    removeButton.CBUIButton_getElement()
                );
            }

            else
            {
                imageWasRemovedCallback =
                undefined;

                removeButton.CBUIButton_getElement().remove();
            }
        }
        // CB_UI_ImageChooser_setImageWasChosenCallback()



        /**
         * @param string newTitle
         *
         * @return undefined
         */
        function
        CB_UI_ImageChooser_setTitle(
            newTitle
        ) // -> undefined
        {
            titleElement.textContent = newTitle;
        }
        // CB_UI_ImageChooser_setTitle()



        let imageChooserController =
        {
            CB_UI_ImageChooser_getElement,

            CB_UI_ImageChooser_setImage,

            CB_UI_ImageChooser_getImageFile,

            CB_UI_ImageChooser_setImageWasChosenCallback,

            CB_UI_ImageChooser_setImageWasRemovedCallback,

            CB_UI_ImageChooser_setTitle,
        };

        return imageChooserController;
    }
    // CBUIImageChooser_create()



    function
    CB_UI_ImageChooser_createImageFileInputElement(
    ) // -> Element
    {
        let inputElement =
        document.createElement(
            "input"
        );

        inputElement.type =
        "file";

        inputElement.style.display =
        "none";

        inputElement.accept =
        "image/jpeg, image/png, image/gif";

        return inputElement;
    }
    // CB_UI_ImageChooser_createImageFileInputElement()

}
)();
