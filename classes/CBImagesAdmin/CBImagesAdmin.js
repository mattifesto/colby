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
        imagesElement.className = "images";

        CBImagesAdmin.fetchImages({
            element: imagesElement
        });

        mainElement.appendChild(imagesElement);
        mainElement.appendChild(CBUI.createHalfSpace());
    },

    /**
     * @param object args
     *
     *      {
     *          ID: hex160
     *          thumbnailURL: string
     *      }
     *
     * @return Element
     */
    createThumbnailElement: function (args) {
        var element = document.createElement("div");
        element.className = "thumbnail";
        var img = document.createElement("img");
        img.src = args.thumbnailURL;
        var link = document.createElement("a");
        link.textContent = ">";

        link.addEventListener("click", function () {
            window.location = "/admin/?c=CBModelInspector&ID=" + args.ID;
        });

        element.appendChild(img);
        element.appendChild(link);

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
                args.element.appendChild(CBImagesAdmin.createThumbnailElement(images[i]));
            }
        }
    },
};

Colby.afterDOMContentLoaded(CBImagesAdmin.init);
