/* globals
    CBImage,
    CBModel,
*/


(function () {
    "use strict";

    window.CBArtwork = {
        getImage: CBArtwork_getImage,
        getMediumImageURL,
        getThumbnailImageURL,
    };



    /* -- accessors -- */



    /**
     * @param object artworkModel
     *
     * @return object|undefined
     */
    function
    CBArtwork_getImage(
        artworkModel
    ) // -> object|undefined
    {
        return CBModel.valueAsModel(
            artworkModel,
            "image",
            'CBImage'
        );
    }
    /* CBArtwork_getImage() */



    /**
     * @return string
     */
    function
    getMediumImageURL(
        artworkModel
    ) /* -> string */
    {
        let imageURL = CBImage.toURL(
            artworkModel.image,
            "rl1280"
        );

        if (
            imageURL !== ""
        ) {
            return imageURL;
        }

        imageURL = CBModel.valueToString(
            artworkModel,
            "mediumImageURL"
        );

        if (
            imageURL !== ""
        ) {
            return imageURL;
        }

        imageURL = CBModel.valueToString(
            artworkModel,
            "largeImageURL"
        );

        if (
            imageURL !== ""
        ) {
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
    function
    getThumbnailImageURL(
        artworkModel
    ) /* -> string */
    {
        let imageURL = CBImage.toURL(
            artworkModel.image,
            "rl320"
        );

        if (
            imageURL !== ""
        ) {
            return imageURL;
        }

        imageURL = CBModel.valueToString(
            artworkModel,
            "thumbnailImageURL"
        );

        if (
            imageURL !== ""
        ) {
            return imageURL;
        }

        imageURL = CBModel.valueToString(
            artworkModel,
            "mediumImageURL"
        );

        if (
            imageURL !== ""
        ) {
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
