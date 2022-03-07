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
            (object)[
                'name' =>
                'realWorldScenario1',

                'type' =>
                'server',
            ],
        ];
    }
    // CBTest_getTests()



    /* -- tests -- */



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
