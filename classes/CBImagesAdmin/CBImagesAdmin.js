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
        let elements = document.getElementsByClassName("CBImagesAdmin");

        if (elements.length > 0) {
            let element = elements.item(0);

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
        imagesElement.className = "CBImagesAdmin_imageList";

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
    createImageElement(
        image
    ) // -> Element
    {
        var element = document.createElement("div");
        element.className = "CBImagesAdmin_image";

        var sectionElement = document.createElement("div");
        sectionElement.className = "section";

        {
            let sectionItemElement = document.createElement("div");
            sectionItemElement.className = "thumbnail";

            let img = document.createElement("img");
            img.src = CBImage.toURL(
                image,
                "rw320"
            );

            sectionItemElement.appendChild(img);
            sectionElement.appendChild(sectionItemElement);
        }

        {
            let sectionItem = CBUISectionItem4.create();
            sectionItem.callback = function () {
                window.location = "/admin/?c=CBModelInspector&ID=" + image.ID;
            };

            let stringsPart = CBUIStringsPart.create();
            stringsPart.string1 = "Inspect";

            stringsPart.element.classList.add("action");

            sectionItem.appendPart(stringsPart);
            sectionElement.appendChild(sectionItem.element);
        }

        element.appendChild(sectionElement);

        return element;
    }
    /* createImageElement() */


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
                    createImageElement(
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
