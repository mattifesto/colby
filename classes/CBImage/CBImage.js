/* global
    CBDataStore,
    CBModel,
*/


(function () {
    "use strict";

    window.CBImage = {
        toURL: CBImage_toUrl,
    };


    /**
     * @param object imageModel
     *
     *      A CBImage model.
     *
     * @param string? filename
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
        filename
    ) {
        let imageModelCBID = CBModel.getCBID(
            imageModel,
        );

        if (
            imageModelCBID === undefined
        ) {
            return "";
        }

        if (
            typeof filename !== "string"
        ) {
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
            typeof imageModel.extension !== "string" ||
            imageModel.extension === ""
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
                    imageModel.extension
                )
            )
        );
    }
    /* CBImage_toUrl() */

})();
