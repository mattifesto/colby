"use strict";
/* jshint strict: global */
/* exported CBImagesAdminPage */
/* global
    CBUI,
    Colby */

var CBImagesAdminPage = {

    /**
     * @return Element
     */
    createElement: function() {
        var element = document.createElement("div");
        element.className = "CBImagesAdminPage";
        var buttonsElement = document.createElement("div");
        buttonsElement.className = "buttons";
        var imagesElement = document.createElement("div");
        imagesElement.className = "images";

        buttonsElement.appendChild(CBUI.createButton({
            callback: function () {
                Colby.callAjaxFunction("CBImageVerificationTask", "startForAllImages")
                    .then(function () { Colby.alert("Verification for all images started."); })
                    .catch(Colby.displayAndReportError);
            },
            text: "Start Verification for All Images",
        }).element);

        CBImagesAdminPage.fetchImages({
            element: imagesElement
        });

        element.appendChild(buttonsElement);
        element.appendChild(imagesElement);
        element.appendChild(CBUI.createHalfSpace());

        return element;
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
        Colby.callAjaxFunction("CBImagesAdminPage", "fetchImages")
            .then(onFulfilled)
            .catch(Colby.displayAndReportError);

        function onFulfilled(images) {
            for (var i = 0; i < images.length; i++) {
                args.element.appendChild(CBImagesAdminPage.createThumbnailElement(images[i]));
            }
        }
    },
};

Colby.afterDOMContentLoaded(function() {
    var main = document.getElementsByTagName("main")[0];

    main.appendChild(CBImagesAdminPage.createElement());
});
