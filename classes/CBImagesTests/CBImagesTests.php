<?php

final class CBImagesTests {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v497.js', cbsysurl()),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return [
            'CBDataStore',
            'Colby',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'name' => 'upload',
                'title' => 'CBImages 01 upload',
            ],
            (object)[
                'name' => 'resize',
                'title' => 'CBImages 02 resize',
                'type' => 'server',
            ],
            (object)[
                'name' => 'deleteByID',
                'title' => 'CBImages 99 deleteByID',
            ],
        ];
    }
    /* CBTest_getTests() */



    /* -- tests -- -- -- -- -- */



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
    static function CBTest_resize(): stdClass {
        CBImages::reduceImage(
            CBTestAdmin::testImageID(),
            'jpeg',
            'rw5000rh5000'
        );

        $filepath1 = CBDataStore::flexpath(
            CBTestAdmin::testImageID(),
            'original.jpeg',
            cbsitedir()
        );

        $filepath2 = CBDataStore::flexpath(
            CBTestAdmin::testImageID(),
            'rw5000rh5000.jpeg',
            cbsitedir()
        );

        $sha1 = sha1_file($filepath1);
        $sha2 = sha1_file($filepath2);

        if ($sha1 !== $sha2) {
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
    /* CBTest_resize() */

}
