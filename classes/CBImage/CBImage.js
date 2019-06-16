"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBImage */
/* global
    CBDataStore,
    CBModel,
*/

var CBImage = {

    /**
     * @param object image
     *
     *      {
     *          ID: ID
     *          filename: string
     *          extension: string
     *      }
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
    toURL: function (image, filename) {
        let imageID = CBModel.valueAsID(image, "ID");

        if (imageID === undefined) {
            return "";
        }

        if (typeof filename !== "string") {
            filename = CBModel.valueToString(image, "filename");
        }

        if (filename === "") {
            return "";
        }

        if (typeof image.extension !== "string" || image.extension === "") {
            return "";
        }

        return "/" + CBDataStore.flexpath(
            imageID,
            filename + "." + image.extension
        );
    },
};
