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
        } else {

            /**
             * Returning only the img element with a max-width will get the job
             * done. The element will change size after the image loads.
             */

            imgElement.src = args.src;
            imgElement.style.display = "block";

            if (args.maxWidth) {
                imgElement.style.maxWidth = args.maxWidth + "px";
            }

            return imgElement;
        }

        containerElement.appendChild(imgElement);
        element.appendChild(containerElement);

        return element;
    },
};
