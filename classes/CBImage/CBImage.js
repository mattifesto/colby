/* global
    CBDataStore,
    CBModel,
*/


(function () {
    "use strict";

    window.CBImage = {
        getExtension: CBImage_getExtension,
        toURL: CBImage_toUrl,
    };



    /* -- accessors -- */



    /**
     * @param object imageModel
     *
     * @return string
     */
    function
    CBImage_getExtension(
        imageModel
    ) {
        return CBModel.valueToString(
            imageModel,
            "extension"
        );
    }
    /* CBImage_getExtension() */



    /* -- functions -- */



    /**
     * @param object imageModel
     *
     *      A CBImage model.
     *
     * @param string? imageResizeOperation
     *
     *      If specified, this value will be used instead of the filename
     *      property value in the image model. This allows the caller to specify
     *      a smaller size, such as "rw320", rather than "original", the most
     *      common filename property value of an image model.
     *
     * @return string
     *
     *      Returns an empty string if there is an issue with the parameters.
     */
    function
    CBImage_toUrl(
        imageModel,
        imageResizeOperation,
        imageExtension
    ) {
        let filename;
        
        let imageModelCBID = CBModel.getCBID(
            imageModel,
        );

        if (
            imageModelCBID === undefined
        ) {
            return "";
        }

        if (
            typeof imageResizeOperation === "string"
        ) {
            filename = imageResizeOperation;
        }

        else {
            filename = CBModel.valueToString(
                imageModel,
                "filename"
            );
        }

        if (
            filename === ""
        ) {
            return "";
        }

        if (
            typeof imageExtension !== "string"
        ) {
            imageExtension = CBImage_getExtension(
                imageModel
            );
        }

        if (
            imageExtension === ""
        ) {
            return "";
        }

        return (
            "/" +
            CBDataStore.flexpath(
                imageModelCBID,
                (
                    filename +
                    "." +
                    imageExtension
                )
            )
        );
    }
    /* CBImage_toUrl() */

})();
