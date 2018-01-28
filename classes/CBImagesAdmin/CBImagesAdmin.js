"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBImagesAdmin */
/* global
    CBUI,
    CBUISectionItem4,
    CBUIStringsPart,
    Colby */

var CBImagesAdmin = {

    /**
     * @return undefined
     */
    init: function() {
        var mainElement = document.getElementsByTagName("main")[0];

        mainElement.classList.add("CBDarkTheme");

        mainElement.appendChild(CBUI.createHalfSpace());

        {
            let sectionElement = CBUI.createSection();
            let sectionItem = CBUISectionItem4.create();
            sectionItem.callback = function () {
                Colby.callAjaxFunction("CBImageVerificationTask", "startForAllImages")
                    .then(function () { Colby.alert("Verification for all images started."); })
                    .catch(Colby.displayAndReportError);
            };

            let stringsPart = CBUIStringsPart.create();
            stringsPart.string1 = "Start Verification for All Images";

            stringsPart.element.classList.add("action");

            sectionItem.appendPart(stringsPart);
            sectionElement.appendChild(sectionItem.element);
            mainElement.appendChild(sectionElement);
            mainElement.appendChild(CBUI.createHalfSpace());
        }

        var imagesElement = document.createElement("div");
        imagesElement.className = "CBImagesAdmin_imageList";

        CBImagesAdmin.fetchImages({
            element: imagesElement
        });

        mainElement.appendChild(imagesElement);
        mainElement.appendChild(CBUI.createHalfSpace());
    },

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
            img.src = Colby.imageToURL(image, "rw320");

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

    /**
     * @param object args
     *
     *      {
     *          element: Element
     *      }
     *
     * @return undefined
     */
    fetchImages: function (args) {
        Colby.callAjaxFunction("CBImagesAdmin", "fetchImages")
            .then(onFulfilled)
            .catch(Colby.displayAndReportError);

        function onFulfilled(images) {
            for (var i = 0; i < images.length; i++) {
                let imageElement = CBImagesAdmin.createImageElement(images[i]);
                args.element.appendChild(imageElement);
            }
        }
    },
};

Colby.afterDOMContentLoaded(CBImagesAdmin.init);
