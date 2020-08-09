"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* globals
    CBImage,
    CBModel,
*/


(function () {

    window.CBArtwork = {
        getMediumImageURL,
        getThumbnailImageURL,
    };



    /**
     *
     */
    function getMediumImageURL(
        artworkModel
    ) {
        let imageURL = CBImage.toURL(
            artworkModel.image,
            "rl1280"
        );

        if (imageURL !== "") {
            return imageURL;
        }

        imageURL = CBModel.valueToString(
            artworkModel,
            "mediumImageURL"
        );

        if (imageURL !== "") {
            return imageURL;
        }

        imageURL = CBModel.valueToString(
            artworkModel,
            "largeImageURL"
        );

        if (imageURL !== "") {
            return imageURL;
        }

        imageURL = CBModel.valueToString(
            artworkModel,
            "thumbnailImageURL"
        );

        return imageURL;
    }
    /* getMediumImageURL() */



    /**
     * @param object artworkModel
     *
     * @return string
     *
     *      Returns an empty string if no URL is available.
     */
    function getThumbnailImageURL(
        artworkModel
    ) {
        let imageURL = CBImage.toURL(
            artworkModel.image,
            "rl320"
        );

        if (imageURL !== "") {
            return imageURL;
        }

        imageURL = CBModel.valueToString(
            artworkModel,
            "thumbnailImageURL"
        );

        if (imageURL !== "") {
            return imageURL;
        }

        imageURL = CBModel.valueToString(
            artworkModel,
            "mediumImageURL"
        );

        if (imageURL !== "") {
            return imageURL;
        }

        imageURL = CBModel.valueToString(
            artworkModel,
            "largeImageURL"
        );

        return imageURL;
    }
    /* getThumbnailImageURL() */


})();
