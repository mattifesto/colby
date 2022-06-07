/* global
    CBAjax,
    CBUIButton,
    CBErrorHandler,
    CBImage,
    CBUIPanel,
    Colby,
*/


(function ()
{
    "use strict";



    let CBImagesAdmin_shared_startImageVerificationButtton;



    Colby.afterDOMContentLoaded(
        function () {
            init();
        }
    );



    /**
     * @return undefined
     */
    function
    init(
    ) // -> undefined
    {
        let elements =
        document.getElementsByClassName(
            "CBImagesAdmin"
        );

        if (
            elements.length > 0
        ) {
            let element =
            elements.item(0);

            element.appendChild(
                createElement()
            );
        }
    }
    /* init() */



    /**
     * @return Element
     */
    function
    createElement(
    ) // -> Element
    {
        let rootElement =
        document.createElement(
            "div"
        );

        rootElement.className =
        "CBImagesAdmin_root_element CBDarkTheme";

        rootElement.appendChild(
            CBImagesAdmin_createStartImageVerificationElement()
        );

        const imageListElement =
        document.createElement(
            "div"
        );

        imageListElement.className =
        "CBImagesAdmin_imageList_element";

        rootElement.appendChild(
            imageListElement
        );

        CBImagesAdmin_fetchImageModels(
            imageListElement
        );

        return rootElement;
    }
    /* createElement() */



    /**
     * @param object (CBImage) image
     *
     * @return Element
     */
    function
    createImageListItemElement(
        imageModel
    ) // -> Element
    {
        let rootElement =
        document.createElement(
            "div"
        );

        rootElement.className =
        "CBImagesAdmin_imageListItem_element";

        let pictureContainerElement =
        document.createElement(
            "div"
        );

        pictureContainerElement.className =
        "CBImagesAdmin_pictureContainer_element";

        rootElement.appendChild(
            pictureContainerElement
        );

        let pictureElement =
        CBImage.createPictureElementWithMaximumDisplayWidthAndHeight(
            imageModel,
            "rw320",
            128,
            128,
            ""
        );

        pictureContainerElement.appendChild(
            pictureElement
        );

        let inspectAnchorElement =
        document.createElement(
            "a"
        );

        inspectAnchorElement.className =
        "CBImagesAdmin_inspect_element";

        inspectAnchorElement.textContent =
        "Inspect";

        inspectAnchorElement.href =
        "/admin/?c=CBModelInspector&ID=" + imageModel.ID;

        rootElement.appendChild(
            inspectAnchorElement
        );

        return rootElement;
    }
    /* createImageListItemElement() */



    /**
     * @return Element
     */
    function
    CBImagesAdmin_createStartImageVerificationElement(
    ) // -> Element
    {
        const button =
        CBUIButton.create();

        CBImagesAdmin_shared_startImageVerificationButtton =
        button;

        button.CBUIButton_setTextContent(
            "Start Verification for All Images"
        );

        button.CBUIButton_addClickEventListener(
            function (
            ) // -> undefined
            {
                CBImagesAdmin_startImageVerification();
            }
        );

        let buttonElement =
        button.CBUIButton_getElement();

        return buttonElement;
    }
    // CBImagesAdmin_createStartImageVerificationElement()



    /**
     * @param Element imageListElement
     *
     * @return Promise -> undefined
     */
    async function
    CBImagesAdmin_fetchImageModels(
        imageListElement
    ) // -> Promise -> [object]
    {
        try
        {
            let imageModels =
            await
            CBAjax.call(
                "CBImagesAdmin",
                "fetchImages"
            );

            imageModels.forEach(
                function (
                    imageModel
                ) // -> undefined
                {
                    let imageListItemElement =
                    createImageListItemElement(
                        imageModel
                    );

                    imageListElement.append(
                        imageListItemElement
                    );
                }
            );
        }

        catch (
            error
        ) {
            CBUIPanel.displayError(
                error
            );

            CBErrorHandler.report(
                error
            );
        }
    }
    /* CBImagesAdmin_fetchImageModels() */



    /**
     * @return undefined
     */
    async function
    CBImagesAdmin_startImageVerification(
    ) // -> undefined
    {
        const button =
        CBImagesAdmin_shared_startImageVerificationButtton;

        if (
            button.CBUIButton_getIsDisabled()
        ) {
            return;
        }

        try
        {
            button.CBUIButton_setIsDisabled(
                true
            );

            await CBAjax.call(
                "CBImageVerificationTask",
                "startForAllImages"
            );

            CBUIPanel.displayText(
                "Verification for all images started."
            );
        }

        catch (
            error
        ) {
            CBUIPanel.displayError(
                error
            );

            CBErrorHandler.report(
                error
            );
        }

        finally
        {
            button.CBUIButton_setIsDisabled(
                false
            );
        }
    }
    // CBImagesAdmin_startImageVerification()

}
)();
