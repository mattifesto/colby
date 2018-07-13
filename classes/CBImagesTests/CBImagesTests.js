"use strict";
/* jshint strict: global */
/* jshint esnext: true */
/* exported CBImagesTests */
/* global
    CBTestAdmin,
    Colby,
*/

/**
 * @NOTE 2017.11.14
 *
 * These test were transplanted from CBTestAdmin. They are verified to
 * work, but the cross object accesses are a bit odd. Consider improving in the
 * future.
 */
var CBImagesTests = {

    /**
     * @NOTE 2017.11.14 Maybe this should be a CBModels test.
     *
     * @return Promise
     */
    deleteByIDTest: function () {
        return Colby.callAjaxFunction("CBModels", "deleteByID", { ID: CBTestAdmin.testImageID })
                    .then(report1)
                    .then(report2);

        function report1(response) {
            var imageURI = "/" + Colby.dataStoreFlexpath(CBTestAdmin.testImageID, "original.jpeg");

            return CBImagesTests.fetchURIDoesExist(imageURI);
        }

        function report2(doesExist) {
            if (doesExist) {
                throw new Error("The image file is available but should not be.");
            }

            return {
                succeeded: true,
            };
        }
    },

    /**
     * @param string URI
     *
     * @return Promise
     */
    fetchURIDoesExist: function (URI) {
        return new Promise(function (resolve, reject) {
            var xhr = new XMLHttpRequest();
            xhr.onloadend = handler;
            xhr.open("HEAD", URI);
            xhr.send();

            function handler() {
                if (xhr.status === 200) {
                    resolve(true);
                } else if (xhr.status === 404) {
                    resolve(false); // The image has been deleted, as expected.
                } else {
                    reject(new Error("Request returned an unexpected status: " + xhr.status));
                }
            }
        });
    },


    /**
     * @return Promise
     */
    uploadTest: function () {
        var URL = "/api/?class=CBImages&function=upload";
        var data = new FormData();

        /**
         * @NOTE 2017.11.14 This is kind of a crazy public property access.
         * However, it does work so think of a better way later if necessary.
         */

        data.append("image", CBTestAdmin.fileInputElement.files[0]);

        return Colby.fetchAjaxResponse(URL, data)
                    .then(report1)
                    .then(report2)
                    .then(report3);

        function report1(response) {
            var image = response.image;

            if (image.extension === "jpeg" &&
                image.filename === "original" &&
                image.height === 900 &&
                image.ID === CBTestAdmin.testImageID &&
                image.width === 1600)
            {
                var imageURI = "/" + Colby.dataStoreFlexpath(CBTestAdmin.testImageID, "original.jpeg");

                return CBImagesTests.fetchURIDoesExist(imageURI);
            } else {
                throw new Error("The image file did not upload correctly.");
            }
        }

        function report2(doesExist) {
            if (doesExist) {
                var imageURI = "/" + Colby.dataStoreFlexpath(CBTestAdmin.testImageID, "rw640.jpeg");

                return CBImagesTests.fetchURIDoesExist(imageURI);
            } else {
                throw new Error("The image file is not available.");
            }
        }

        function report3(doesExist) {
            if (!doesExist) {
                throw new Error("The image file is not available but should be.");
            }
        }
    },
};
