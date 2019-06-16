"use strict";
/* jshint strict: global */
/* jshint esnext: true */
/* exported CBImagesTests */
/* global
    CBDataStore,
    CBTestAdmin,
    Colby,
*/

/**
 * @NOTE 2017_11_14
 *
 * These tests were transplanted from CBTestAdmin. They are verified to work,
 * but the cross object accesses are a bit odd. Consider improving in the
 * future. The value of "CBTestAdmin.testImageID" should be provided as a
 * JavaScript variable by this class's PHP implementation and any use of
 * CBTestAdmin should be removed.
 */
var CBImagesTests = {

    /**
     * @NOTE 2017_11_14 Maybe this should be a CBModels test.
     *
     * @return Promise
     */
    CBTest_deleteByID: function () {
        let promise = Colby.callAjaxFunction(
            "CBModels",
            "deleteByID",
            {
                ID: CBTestAdmin.testImageID
            }
        ).then(
            function (value) {
                return CBTest_deleteByID_report1(value);
            }
        ).then(
            function (value) {
                return CBTest_deleteByID_report2(value);
            }
        );

        return promise;


        /* -- closures -- -- -- -- -- */

        /**
         * @return Promise -> bool
         */
        function CBTest_deleteByID_report1() {
            var imageURI =
            "/" +
            CBDataStore.flexpath(
                CBTestAdmin.testImageID,
                "original.jpeg"
            );

            return CBImagesTests.fetchURIDoesExist(imageURI);
        }
        /* CBTest_deleteByID_report1() */


        /**
         * @param bool doesExist
         *
         * @return object
         */
        function CBTest_deleteByID_report2(doesExist) {
            if (doesExist) {
                throw new Error("The image file is available but should not be.");
            }

            return {
                succeeded: true,
            };
        }
        /* CBTest_deleteByID_report2() */
    },
    /* CBTest_deleteByID() */


    /**
     * @param string URI
     *
     * @return Promise
     */
    fetchURIDoesExist: function (URI) {
        return new Promise(
            function (resolve, reject) {
                let xhr = new XMLHttpRequest();
                xhr.onloadend = handler;
                xhr.open("HEAD", URI);
                xhr.send();

                function handler() {
                    if (xhr.status === 200) {
                        resolve(true);
                    } else if (xhr.status === 404) {
                        resolve(false); // The image has been deleted, as expected.
                    } else {
                        reject(
                            Error(
                                "Request returned an unexpected status: " +
                                xhr.status
                            )
                        );
                    }
                }
            }
        );
    },
    /* fetchURIDoesExist() */


    /**
     * @return Promise
     */
    CBTest_upload: function () {
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
                var imageURI =
                "/" +
                CBDataStore.flexpath(
                    CBTestAdmin.testImageID,
                    "original.jpeg"
                );

                return CBImagesTests.fetchURIDoesExist(imageURI);
            } else {
                throw new Error("The image file did not upload correctly.");
            }
        }

        function report2(doesExist) {
            if (doesExist) {
                var imageURI =
                "/" +
                CBDataStore.flexpath(
                    CBTestAdmin.testImageID,
                    "rw640.jpeg"
                );

                return CBImagesTests.fetchURIDoesExist(imageURI);
            } else {
                throw new Error("The image file is not available.");
            }
        }

        function report3(doesExist) {
            if (!doesExist) {
                throw new Error("The image file is not available but should be.");
            }

            return {
                succeeded: true,
            };
        }
    },
    /* CBTest_upload() */
};
/* CBImagesTests */
