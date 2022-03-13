<?php

final class
CBImagesTests
{
    /* -- CBHTMLOutput interfaces -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array {
        return [
            Colby::flexpath(
                __CLASS__,
                'v656.js',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_requiredClassNames(
    ):array {
        return [
            'CBAjax',
            'CBDataStore',
            'Colby',

            /* 'CBTestAdmin', (used but would cause circular dependency) */
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function
    CBTest_getTests(
    ): array
    {
        return
        [
            /**
             * @NOTE 2022_02_18
             *
             *      These tests run in the order they are specified, and for
             *      this class the order is intentional and required.
             */

            (object)[
                'name' => 'upload',
            ],
            (object)[
                'name' => 'resize',
                'type' => 'server',
            ],
            (object)[
                'name' => 'deleteByID',
            ],

            /* order no longer matters after this */

            (object)
            [
                'name' =>
                'convertToWebP',

                'type' =>
                'server',
            ],
        ];
    }
    /* CBTest_getTests() */



    /* -- tests -- */



    /**
     * @return object
     */
    static function
    convertToWebP(
    ): stdClass
    {
        $sampleImageModelCBID =
        CB_SampleImages::getSampleImageModelCBID_1000x5000();

        $sampleWebPImageFilepath =
        CBDataStore::flexpath(
            $sampleImageModelCBID,
            'rw2000.webp',
            cb_document_root_directory()
        );



        if (
            file_exists(
                $sampleWebPImageFilepath
            )
        ) {
            $expectedResult =
            true;

            $actualResult =
            unlink(
                $sampleWebPImageFilepath
            );

            if (
                $actualResult !== $expectedResult
            ) {
                return CBTest::resultMismatchFailure(
                    'prepare for test',
                    $actualResult,
                    $expectedResult
                );
            }
        }



        CBImages::reduceImage(
            $sampleImageModelCBID,
            'webp',
            'rw2000'
        );

        $imageInformation =
        getimagesize(
            $sampleWebPImageFilepath
        );

        $expectedResult =
        IMAGETYPE_WEBP;

        $actualResult =
        $imageInformation[2];

        if (
            $actualResult !== $expectedResult
        ) {
            return CBTest::resultMismatchFailure(
                'image type check',
                $actualResult,
                $expectedResult
            );
        }



        $expectedResult =
        1000;

        $actualResult =
        $imageInformation[0];

        if (
            $actualResult !== $expectedResult
        ) {
            return CBTest::resultMismatchFailure(
                'image type check',
                $actualResult,
                $expectedResult
            );
        }



        $expectedResult =
        5000;

        $actualResult =
        $imageInformation[1];

        if (
            $actualResult !== $expectedResult
        ) {
            return CBTest::resultMismatchFailure(
                'image type check',
                $actualResult,
                $expectedResult
            );
        }



        return
        (object)
        [
            'succeeded' =>
            true,
        ];
    }
    // convertToWebP()



    /**
     * When a request is made to reduce an image to a size larger than the
     * original, the original file should just be copied to the new filename and
     * the files should be exactly the same.
     *
     * The test image should have been uploaded by earlier tests in the overall
     * testing process.
     *
     * @return object
     */
    static function
    resize(
    ): stdClass {
        CBImages::reduceImage(
            CBImagesTests::getTestImageModelCBID(),
            'jpeg',
            'rw5000rh5000'
        );

        $filepath1 = CBDataStore::flexpath(
            CBImagesTests::getTestImageModelCBID(),
            'original.jpeg',
            cbsitedir()
        );

        $filepath2 = CBDataStore::flexpath(
            CBImagesTests::getTestImageModelCBID(),
            'rw5000rh5000.jpeg',
            cbsitedir()
        );

        $sha1 = sha1_file(
            $filepath1
        );

        $sha2 = sha1_file(
            $filepath2
        );

        if (
            $sha1 !== $sha2
        ) {
            return CBTest::resultMismatchFailure(
                'sha',
                $sha1,
                $sha2
            );
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* resize() */



    /**
     * @NOTE 2022_02_18
     *
     *      This CBID is also specified in CBTestAdmin.js and I'm not sure why
     *      it's specified there. There are also comments saying this class
     *      depends on CBTestAdmin and if it's just for this CBID that
     *      dependency should be removed. I'm too busy now to investigate.
     *
     * @return CBID
     */
    static function
    getTestImageModelCBID(
    ): string {
        return '3dd8e721048bbe8ea5f0c043fab73277a0b0044c';
    }
    /* getTestImageModelCBID() */

}
