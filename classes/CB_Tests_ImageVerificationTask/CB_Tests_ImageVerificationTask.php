<?php

final class
CB_Tests_ImageVerificationTask
{
    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function
    CBTest_getTests(
    ): array
    {
        return [
            (object)
            [
                'name' =>
                'realWorldScenario1',

                'type' =>
                'server',
            ],
            (object)[
                'name' =>
                'checkForInvalidWebPFiles',

                'type' =>
                'server',
            ],
        ];
    }
    // CBTest_getTests()



    /* -- tests -- */



    /**
     * At one point if a larger-than-original webp version of a non-webp
     * original image was requested the original non-webp file would be copied
     * and have the extension changed to webp. This was a bug.
     * CBImageVerificationTask now checks for webp files that aren't actually
     * webp files and deletes them so that they can be regenerated as actual
     * webp files.
     *
     * @return object
     */
    static function
    checkForInvalidWebPFiles(
    ): stdClass
    {
        $sampleImageModelCBID =
        CB_SampleImages::getSampleImageModelCBID_1000x5000();

        $badWebPImageFilepath =
        CBDataStore::flexpath(
            $sampleImageModelCBID,
            'rw2100.webp',
            cb_document_root_directory()
        );



        if (
            file_exists(
                $badWebPImageFilepath
            )
        ) {
            $expectedResult =
            true;

            $actualResult =
            unlink(
                $badWebPImageFilepath
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



        $goodWebPImageFilepath =
        CBDataStore::flexpath(
            $sampleImageModelCBID,
            'rw320.webp',
            cb_document_root_directory()
        );

        if (
            file_exists(
                $goodWebPImageFilepath
            )
        ) {
            $expectedResult =
            true;

            $actualResult =
            unlink(
                $goodWebPImageFilepath
            );

            if (
                $actualResult !== $expectedResult
            ) {
                return CBTest::resultMismatchFailure(
                    'prepare for test filepath 2',
                    $actualResult,
                    $expectedResult
                );
            }
        }



        $sampleOriginalImageFilepath =
        CBDataStore::flexpath(
            $sampleImageModelCBID,
            'original.png',
            cb_document_root_directory()
        );



        $expectedResult =
        true;

        $actualResult =
        copy(
            $sampleOriginalImageFilepath,
            $badWebPImageFilepath
        );

        if (
            $actualResult !== $expectedResult
        ) {
            return CBTest::resultMismatchFailure(
                'copy original file to webp filepath',
                $actualResult,
                $expectedResult
            );
        }



        $expectedResult =
        true;

        $actualResult =
        file_exists(
            $badWebPImageFilepath
        );

        if (
            $actualResult !== $expectedResult
        ) {
            return CBTest::resultMismatchFailure(
                'verify that the bad image file exists',
                $actualResult,
                $expectedResult
            );
        }



        CBImages::reduceImage(
            $sampleImageModelCBID,
            'webp',
            'rw320'
        );

        CBTasks2::runSpecificTask(
            'CBImageVerificationTask',
            $sampleImageModelCBID
        );

        $expectedResult =
        false;

        $actualResult =
        file_exists(
            $badWebPImageFilepath
        );

        if (
            $actualResult !== $expectedResult
        ) {
            return CBTest::resultMismatchFailure(
                'the bad image file was not deleted',
                $actualResult,
                $expectedResult
            );
        }



        $expectedResult =
        true;

        $actualResult =
        file_exists(
            $goodWebPImageFilepath
        );

        if (
            $actualResult !== $expectedResult
        ) {
            return CBTest::resultMismatchFailure(
                'the good image file was not preserved',
                $actualResult,
                $expectedResult
            );
        }



        /* done */

        return
        (object)[
            'succeeded' => true,
        ];
    }
    // checkForInvalidWebPFiles()



    /**
     * This test replicates a real world scenario that occurred:
     *
     *      - There was an original image file named "original."
     *      - There was a row in the CBImages table
     *      - There was no model
     *
     * The image verification task should sense this scenario and delete the
     * CBImages row and the data store.
     *
     * @return object
     */
    static function
    realWorldScenario1(
    ): stdClass
    {
        $testImageCBID =
        'fb9c0ef5734de2b38dd615502a70af2e0f5f6c93';

        $unixTimestamp =
        time();

        $extension =
        '';

        CBImages::updateRow(
            $testImageCBID,
            $unixTimestamp,
            $extension
        );

        CBDataStore::create(
            $testImageCBID
        );

        $originalImageFilepath =
        CBDataStore::flexpath(
            $testImageCBID,
            'original.',
            cb_document_root_directory()
        );

        touch(
            $originalImageFilepath
        );



        CBLog::buffer(
            function () use (
                $testImageCBID
            ) {
                try {
                    CBSlack::disable();

                    CBTasks2::runSpecificTask(
                        'CBImageVerificationTask',
                        $testImageCBID
                    );
                }

                catch (
                    Throwable $throwable
                ) {
                    throw $throwable;
                }

                finally
                {
                    CBSlack::enable();
                }
            }
        );



        /* test */

        $dataStoreDirectoryFromDocumentRoot =
        CBDataStore::directoryForID(
            $testImageCBID
        );

        $expectedResult =
        false;

        $actualResult =
        is_dir(
            $dataStoreDirectoryFromDocumentRoot
        );

        if (
            $actualResult !== $expectedResult
        ) {
            return
            CBTest::resultMismatchFailure(
                CBConvert::stringToCleanLine(<<<EOT

                    the data store directory
                    '${dataStoreDirectoryFromDocumentRoot}' still exists

                EOT),
                $actualResult,
                $expectedResult
            );
        }



        /* test */

        $expectedResult =
        null;

        $actualResult =
        CBImages::fetchRowForImageCBID(
            $testImageCBID
        );

        if (
            $actualResult !== $expectedResult
        ) {
            return
            CBTest::resultMismatchFailure(
                'CBImages table row',
                $actualResult,
                $expectedResult
            );
        }


        /* done */

        return
        (object)[
            'succeeded' => true,
        ];
    }
    // realWorldScenario1()

}
