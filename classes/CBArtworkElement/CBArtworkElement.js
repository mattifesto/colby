"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBArtworkElement */
/* globals
    CBModel,
*/

var CBArtworkElement = {

    /**
     * For a better understanding of this function and CBArtworkElement, view
     * the documentation at:
     *
     *      /admin/?c=CBArtworkElementDocumentation
     *
     * @param object args
     *
     *      {
     *          URL: string
     *
     *              The URL for the image.
     *
     *          aspectRatioWidth: number (default: 1)
     *          aspectRatioHeight: number (default: 1)
     *
     *              These arguments specify the aspect ratio of the container.
     *              Callers often provide the original image dimensions in
     *              pixels, because that is what is available in the CBImage
     *              model. Other times, such as when fitting the image in a
     *              square aspect ratio container, they may be 1 and 1.
     *
     *          maxWidth: number (optional)
     *          maxHeight: number (optional)
     *      }
     *
     * @return Element
     */
    create: function (args) {
        if (!args.image && !args.src) {
            return document.createComment(" CBArtworkElement: no image data was provided ");
        }

        var element = document.createElement("div");
        element.className = "CBArtworkElement";
        element.style.width = args.width || (args.maxWidth ? args.maxWidth + "px" : "100vw");

        var containerElement = document.createElement("div");
        var imgElement = document.createElement("img");
        imgElement.style.width = "100%";

        if (args.image) {
            imgElement.src = Colby.imageToURL(args.image, args.filename || "rw1280");
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

    /**
     * @param number? maxWidth
     * @param number? maxHeight
     * @param number aspectRatioWidth
     * @param number aspectRatioHeight
     *
     * @return number?
     *
     *      If neither maxWidth nor maxHeight are provided, undefined will be
     *      returned.
     */
    calculateMaxWidth: function(
        maxWidth,
        maxHeight,
        aspectRatioWidth,
        aspectRatioHeight
    ) {
        let result;

        if (maxHeight !== undefined) {
            result = maxHeight * (aspectRatioWidth / aspectRatioHeight);

            if (maxWidth !== undefined) {
                result = Math.min(result, maxWidth);
            }
        } else if (maxWidth !== undefined) {
            result = maxWidth;
        }

        return result;
    },
};
