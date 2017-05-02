"use strict"; /* jshint strict: global */
/* global
    Colby */

var CBArtworkElement = {

    /**
     * @param object? args.image
     * @param string? args.src
     * @param int? args.maxWidth
     *
     * @return Element
     */
    create: function (args) {
        if (!args.image && !args.src) {
            return document.createComment(" CBArtworkElement: no image data was provided ");
        }

        var element = document.createElement("div");
        element.className = "CBArtworkElement";
        element.style.width = "100%";

        if (args.maxWidth) {
            element.style.maxWidth = args.maxWidth + "px";
        }

        var containerElement = document.createElement("div");
        var imgElement = document.createElement("img");
        imgElement.style.width = "100%";

        if (args.image) {
            imgElement.src = Colby.imageToURL(args.image, "rw1280");
            imgElement.style.position = "absolute";
            imgElement.style.top = "0";
            imgElement.style.left = "0";
            containerElement.style.overflow = "hidden";
            containerElement.style.position = "relative";
            containerElement.style.paddingBottom = ((args.image.height / args.image.width) * 100) + "%";
        } else { /* deprecated */
            var image = Colby.URIToImage(args.src);

            if (image) {
                imgElement.src = Colby.imageToURL(image, "rw1280");
            } else {
                imgElement.src = args.src;
            }
        }

        containerElement.appendChild(imgElement);
        element.appendChild(containerElement);

        return element;
    },
};
