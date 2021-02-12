"use strict";
/* jshint strict: global */
/* exported CBImagesTests */
/* global
    CBAjax,
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
        let promise = CBAjax.call(
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
            /**
             * Random data is added to the URL to avoid the cache.
             */
            let imageURL = (
                "/" +
                CBDataStore.flexpath(
                    CBTestAdmin.testImageID,
                    "original.jpeg"
                ) +
                "?random=" +
                Colby.random160()
            );

            return CBImagesTests.fetchURIDoesExist(
                imageURL
            );
        }
        /* CBTest_deleteByID_report1() */



        /**
         * @param bool doesExist
         *
         * @return object
         */
        function CBTest_deleteByID_report2(doesExist) {
            if (doesExist) {
                throw new Error(
                    "The image file is available but should not be."
                );
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
                        // The image has been deleted, as expected.
                        resolve(false);
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
        let promise = CBAjax.call(
            "CBImages",
            "upload",
            {},
            CBTestAdmin.fileInputElement.files[0]
        ).then(
            report1
        ).then(
            report2
        ).then(
            report3
        );

        return promise;


        /* -- closures -- -- -- -- -- */

        function report1(imageModel) {
            if (
                imageModel.extension === "jpeg" &&
                imageModel.filename === "original" &&
                imageModel.height === 900 &&
                imageModel.ID === CBTestAdmin.testImageID &&
                imageModel.width === 1600
            ) {
                /**
                 * Random data is added to the URL to avoid the cache.
                 */
                let imageURL = (
                    "/" +
                    CBDataStore.flexpath(
                        CBTestAdmin.testImageID,
                        "original.jpeg"
                    ) +
                    "?random=" +
                    Colby.random160()
                );

                return CBImagesTests.fetchURIDoesExist(
                    imageURL
                );
            } else {
                throw new Error(
                    "The image file did not upload correctly."
                );
            }
        }
        /* report1() */



        function report2(doesExist) {
            if (doesExist) {
                /**
                 * Random data is added to the URL to avoid the cache.
                 */
                let imageURL = (
                    "/" +
                    CBDataStore.flexpath(
                        CBTestAdmin.testImageID,
                        "rw640.jpeg"
                    ) +
                    "?random=" +
                    Colby.random160()
                );

                return CBImagesTests.fetchURIDoesExist(
                    imageURL
                );
            } else {
                throw new Error(
                    "The image file is not available."
                );
            }
        }
        /* report2() */



        function report3(doesExist) {
            if (!doesExist) {
                throw new Error(
                    "The image file is not available but should be."
                );
            }

            return {
                succeeded: true,
            };
        }
        /* report3() */

    },
    /* CBTest_upload() */

};
/* CBImagesTests */
