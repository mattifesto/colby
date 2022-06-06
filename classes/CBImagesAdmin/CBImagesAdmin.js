/* global
    CBAjax,
    CBErrorHandler,
    CBImage,
    CBUI,
    CBUIPanel,
    CBUISectionItem4,
    CBUIStringsPart,
    Colby,
*/


(function ()
{
    "use strict";



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
        let element = CBUI.createElement(
            "CBUIRoot CBDarkTheme"
        );

        element.appendChild(
            CBUI.createHalfSpace()
        );

        {
            let sectionElement = CBUI.createSection();
            let sectionItem = CBUISectionItem4.create();

            sectionItem.callback = function () {
                CBAjax.call(
                    "CBImageVerificationTask",
                    "startForAllImages"
                ).then(
                    function () {
                        CBUIPanel.displayText(
                            "Verification for all images started."
                        );
                    }
                ).catch(
                    function (error) {
                        CBUIPanel.displayError(error);
                        CBErrorHandler.report(error);
                    }
                );
            };

            let stringsPart = CBUIStringsPart.create();
            stringsPart.string1 = "Start Verification for All Images";

            stringsPart.element.classList.add("action");

            sectionItem.appendPart(stringsPart);
            sectionElement.appendChild(sectionItem.element);
            element.appendChild(sectionElement);
            element.appendChild(CBUI.createHalfSpace());
        }

        var imagesElement = document.createElement("div");
        imagesElement.className = "CBImagesAdmin_imageList_element";

        fetchImages(
            {
                element: imagesElement
            }
        );

        element.appendChild(imagesElement);
        element.appendChild(CBUI.createHalfSpace());

        return element;
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
     * @param object args
     *
     *      {
     *          element: Element
     *      }
     *
     * @return Promise -> undefined
     */
    function
    fetchImages(
        args
    ) // -> Promise -> undefined
    {
        let promise = CBAjax.call(
            "CBImagesAdmin",
            "fetchImages"
        ).then(
            function (images) {
                for (var i = 0; i < images.length; i++) {
                    let imageElement =
                    createImageListItemElement(
                        images[i]
                    );

                    args.element.appendChild(imageElement);
                }
            }
        ).catch(
            function (error) {
                CBUIPanel.displayError(error);
                CBErrorHandler.report(error);
            }
        );

        return promise;
    }
    /* fetchImages() */

}
)();
