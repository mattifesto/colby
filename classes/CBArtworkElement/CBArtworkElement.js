"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBArtworkElement */
/* globals
    CBModel,
*/


(function () {

    window.CBArtworkElement = {
        create,
    };

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
     *
     *              The units for these properties are CSS pixels.
     *
     *          linkURL: string
     *
     *              If specified, the main artwork element will be an anchor
     *              instead of a div and like to this URL.
     *      }
     *
     * @return Element
     */
    function create(
        args
    ) {
        let URL = CBModel.valueToString(
            args,
            "URL"
        );

        let aspectRatioWidth = CBModel.valueAsNumber(
            args,
            'aspectRatioWidth'
        ) || 1;

        let aspectRatioHeight = CBModel.valueAsNumber(
            args,
            'aspectRatioHeight'
        ) || 1;

        let maxWidth = CBModel.valueAsNumber(
            args,
            'maxWidth'
        );

        let maxHeight = CBModel.valueAsNumber(
            args,
            'maxHeight'
        );

        let calculatedMaxWidth = calculateMaxWidth(
            maxWidth,
            maxHeight,
            aspectRatioWidth,
            aspectRatioHeight
        );

        let linkURL = CBModel.valueToString(
            args,
            "linkURL"
        ).trim();

        /* outer element */

        let outerElement;

        if (linkURL === "") {
            outerElement = document.createElement("div");
        } else {
            outerElement = document.createElement("a");
            outerElement.href = linkURL;
        }

        outerElement.className = "CBArtworkElement";

        if (calculatedMaxWidth !== undefined) {
            outerElement.style.width = calculatedMaxWidth + "px";
        } else {
            outerElement.style.width = "100vw";
        }

        /* inner element */

        var innerElement = document.createElement("div");

        {
            let ratio = (aspectRatioHeight / aspectRatioWidth);
            let percent = ratio * 100;

            innerElement.style.paddingBottom = percent + "%";
        }

        /* image element */

        var imageElement = document.createElement("img");
        imageElement.src = URL;

        innerElement.appendChild(imageElement);
        outerElement.appendChild(innerElement);

        return outerElement;
    }
    /* create() */



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
    function calculateMaxWidth(
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
    }
    /* calculateMaxWidth() */

})();
