"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBImagesAdmin */
/* global
    CBImage,
    CBUI,
    CBUISectionItem4,
    CBUIStringsPart,
    Colby,
*/

var CBImagesAdmin = {

    /**
     * @return undefined
     */
    init: function() {
        let elements = document.getElementsByClassName("CBImagesAdmin");

        if (elements.length > 0) {
            let element = elements.item(0);

            element.appendChild(
                CBImagesAdmin.createElement()
            );
        }
    },
    /* init() */


    /**
     * @return Element
     */
    createElement: function () {
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
                Colby.callAjaxFunction(
                    "CBImageVerificationTask",
                    "startForAllImages"
                ).then(
                    function () {
                        Colby.alert("Verification for all images started.");
                    }
                ).catch(
                    Colby.displayAndReportError
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

        CBImagesAdmin.fetchImages(
            {
                element: imagesElement
            }
        );

        element.appendChild(imagesElement);
        element.appendChild(CBUI.createHalfSpace());

        return element;
    },
    /* createElement() */


    /**
     * @param object (CBImage) image
     *
     * @return Element
     */
    createImageElement: function (image) {
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
    },
    /* createImageElement() */


    /**
     * @param object args
     *
     *      {
     *          element: Element
     *      }
     *
     * @return Promise
     */
    fetchImages: function (args) {
        let promise = Colby.callAjaxFunction(
            "CBImagesAdmin",
            "fetchImages"
        ).then(
            fetchImages_onFulfilled
        ).catch(
            Colby.displayAndReportError
        );

        return promise;


        /* -- closures -- -- -- -- -- */

        /**
         * @param [object] images
         *
         * @return undefined
         */
        function fetchImages_onFulfilled(images) {
            for (var i = 0; i < images.length; i++) {
                let imageElement = CBImagesAdmin.createImageElement(
                    images[i]
                );

                args.element.appendChild(imageElement);
            }
        }
        /* fetchImages_onFulfilled() */
    },
    /* fetchImages() */
};
/* CBImagesAdmin */

Colby.afterDOMContentLoaded(CBImagesAdmin.init);
