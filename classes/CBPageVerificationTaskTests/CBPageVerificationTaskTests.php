<?php

final class CBPageVerificationTaskTests {

    /**
     * @return object
     */
    static function CBTest_findDeprecatedSubviewClassNames(): stdClass {
        $spec = CBPageVerificationTaskTests::specWithDeprecatedAndUnsupportedViews();
        $expectedDeprecatedSubviewClassNames = [
            'CBThemedTextView',
        ];

        $actualDeprecatedSubviewClassNames =
            CBPageVerificationTask::findDeprecatedSubviewClassNames($spec);

        if ($actualDeprecatedSubviewClassNames != $expectedDeprecatedSubviewClassNames) {
            return CBTest::resultMismatchFailure(
                'Subtest 1',
                $actualDeprecatedSubviewClassNames,
                $expectedDeprecatedSubviewClassNames
            );
        }

        return (object)[
            'succeeded' => true,
        ];
    }

    /**
     * @return object
     */
    static function CBTest_hasColbyPagesRow(): stdClass {
        $ID = '722880dadcef3874157d086b4eceeae83194173f';
        $spec = (object)[
            'className' => 'CBViewPage',
            'ID' => $ID,
            'title' => 'Test Page For ' . __METHOD__ . '()',
        ];

        CBDB::transaction(function () use ($ID) {
            CBModels::deleteByID($ID);
        });

        $result = CBPageVerificationTask::run($ID);
        $actual = $result->hasColbyPagesRow;
        $expected = false;

        if ($actual !== $expected) {
            return (object)[
                'failed' => true,
                'message' =>
                    "Test 1 failed:\n\n" .
                    CBConvertTests::resultAndExpectedToMessage($actual, $expected),
            ];
        }

        CBDB::transaction(function () use ($spec) {
            CBModels::save($spec);
        });

        $result = CBPageVerificationTask::run($ID);
        $actual = $result->hasColbyPagesRow;
        $expected = true;

        if ($actual !== $expected) {
            return (object)[
                'failed' => true,
                'message' =>
                    "Test 2 failed:\n\n" .
                    CBConvertTests::resultAndExpectedToMessage($actual, $expected),
            ];
        }

        CBDB::transaction(function () use ($ID) {
            CBModels::deleteByID($ID);
        });

        return (object)[
            'succeeded' => true,
        ];
    }

    /**
     * This test creates a page with a `thumbnailURL` referencing an old style
     * image data store and then runs CBPageVerificationTask on it to make sure
     * that the image is imported and the spec is upgraded to use the `image`
     * property instead of `thumbnailURL`.
     *
     * @return null
     */
    static function importThumbnailURLToImageTest() {
        $pageID = '4a7bc517a928056f9518d839881cc9f49ea10c0a';
        $temporaryImageDataStoreID = 'a66a45225d071a4f6e65c475ece1810ac4dec45a';

        CBDB::transaction(function () use ($pageID) {
            CBModels::deleteByID([$pageID]);
        });

        CBModels::deleteByID($temporaryImageDataStoreID);
        CBModels::deleteByID(CBTestAdmin::testImageID());

        $testImageFilepath = CBTestAdmin::testImageFilepath();
        $temporaryImageFilepath = CBDataStore::flexpath($temporaryImageDataStoreID, 'test.jpeg', cbsitedir());
        $temporaryImageURL = CBDataStore::flexpath($temporaryImageDataStoreID, 'test.jpeg', cbsiteurl());

        CBDataStore::create($temporaryImageDataStoreID);
        copy($testImageFilepath, $temporaryImageFilepath);

        $initialPageSspec = (object)[
            'isTest' => true,
            'className' => 'CBViewPage',
            'ID' => $pageID,
            'title' => 'Test Page for ' . __METHOD__ . '()',
            'thumbnailURL' => $temporaryImageURL,
        ];

        CBDB::transaction(function () use ($initialPageSspec) {
            CBModels::save($initialPageSspec);
        });

        CBTasks2::runSpecificTask('CBPageVerificationTask', $pageID);

        $updatedPageSpec = CBModels::fetchSpecByID($pageID);

        if (!empty($updatedPageSpec->thumbnailURL)) {
            throw new Exception('The `thumbnailURL` property is still set on the updated page spec.');
        }

        if (empty($updatedPageSpec->deprecatedThumbnailURL)) {
            throw new Exception('The `deprecatedThumbnailURL` property should be set on the updated page spec.');
        }

        $resultImageID = CBModel::value($updatedPageSpec, 'image.ID');
        $expectedImageID = CBTestAdmin::testImageID();

        if ($resultImageID !== $expectedImageID) {
            $resultImageIDAsJSON = json_encode($resultImageID);
            $expectedImageIDAsJSON = json_encode($expectedImageID);
            throw new Exception("1: The page spec \"image.ID\" property is {$resultImageIDAsJSON} but {$expectedImageIDAsJSON} was expected.");
        }

        // clean up

        CBDB::transaction(function () use ($pageID) {
            CBModels::deleteByID([$pageID]);
        });

        CBModels::deleteByID($temporaryImageDataStoreID);
        CBModels::deleteByID(CBTestAdmin::testImageID());
    }

    /**
     * This test exists to ensure code coverage. At this point, it does not
     * ensure that the tested functionality has actually run.
     *
     * Manually, the log can be checked for an warning entry after this test is
     * run to ensure that the functionality worked properly during the test.
     *
     * @return object
     */
    static function CBTest_invalidImageProperty(): stdClass {
        $ID = '5cf7dc1d21b1c70d62eeede0b9558d63f91781a3';
        $spec = (object)[
            'className' => 'CBViewPage',
            'ID' => $ID,
            'image' => 'This test image property value is a string. A valid property value would be a CBImage spec.',
            'title' => 'Test Page for CBTest_invalidImageProperty() in CBPageVerificationTaskTests',
        ];

        CBDB::transaction(function () use ($ID) {
            CBModels::deleteByID($ID);
        });

        CBDB::transaction(function () use ($spec) {
            CBModels::save($spec);
        });

        $taskWasRun = CBTasks2::runSpecificTask(
            'CBPageVerificationTask',
            $ID
        );

        if (!$taskWasRun) {
            throw new Exception("The task was not run.");
        }

        /**
         * A log entry should be created, no current way to verify.
         */

        CBDB::transaction(function () use ($ID) {
            CBModels::deleteByID($ID);
        });

        return (object)[
            'succeeded' => true,
        ];
    }

    /**
     * @return ?object
     */
    static function rowWithNoModelTest(): ?stdClass {
        $ID = CBPageVerificationTaskTests::createPagesRowAndDataStoreWithoutModel();
        $IDAsSQL = CBHex160::toSQL($ID);
        $result = CBPageVerificationTask::run($ID);

        if (CBDB::SQLToValue("SELECT COUNT(*) FROM ColbyPages where archiveID = {$IDAsSQL}")) {
            return (object)[
                'failed' => true,
                'message' =>
                    'The ColbyPages row should have been deleted.'
            ];
        }

        if (is_dir(CBDataStore::directoryForID($ID))) {
            return (object)[
                'failed' => true,
                'message' =>
                    'The data store directory should have been deleted.'
            ];
        }

        return null;
    }

    /**
     * This test creates a page that has a `thumbnailURL` that references an
     * image from a CBImage. The test then runs CBPageVerificationTask on the
     * page to make sure the `thumbnailURL` is upgraded to an `image` for the
     * same CBImage.
     *
     * @return object
     */
    static function CBTest_upgradeThumbnailURLToImage(): stdClass {
        $pageID = 'e87c8eef4953d3060faaa2e3597c730326adfc29';

        CBDB::transaction(function () use ($pageID) {
            CBModels::deleteByID([$pageID]);
        });

        CBModels::deleteByID(CBTestAdmin::testImageID());

        $testImage = CBImages::URIToCBImage(CBTestAdmin::testImageFilepath());

        if ($testImage->ID !== CBTestAdmin::testImageID()) {
            throw new Exception('2: The imported test image ID is not what was expected.');
        }

        $testImageURL = CBDataStore::flexpath($testImage->ID, 'rw640.jpeg', cbsiteurl());

        $initialPageSspec = (object)[
            'isTest' => true,
            'className' => 'CBViewPage',
            'ID' => $pageID,
            'title' => 'Test Page for ' . __METHOD__ . '()',
            'thumbnailURL' => $testImageURL,
        ];

        CBDB::transaction(function () use ($initialPageSspec) {
            CBModels::save($initialPageSspec);
        });

        CBLog::bufferStart();

        CBTasks2::runSpecificTask('CBPageVerificationTask', $pageID);

        $buffer = CBLog::bufferContents();

        CBLog::bufferEndClean();

        $bufferIsValid = function ($buffer): bool {
            if (count($buffer) !== 3) {
                return false;
            }

            $sourceID = CBModel::valueAsID($buffer[0], 'sourceID');

            if ($sourceID !== '0099cecb597038d4bf5f182965271e25cc60c070') {
                return false;
            }

            return true;
        };

        if (!$bufferIsValid($buffer)) {
            $bufferAsMessage = CBMessageMarkup::stringToMessage(
                CBConvert::valueToPrettyJSON($buffer)
            );

            $message = <<<EOT

                The log entry buffer is not what was expected:

                --- pre\n{$bufferAsMessage}
                ---

EOT;

            return (object)[
                'message' => $message,
            ];
        }

        $updatedPageSpec = CBModels::fetchSpecByID($pageID);

        if (!empty($updatedPageSpec->thumbnailURL)) {
            throw new Exception('2: The `thumbnailURL` property is still set on the updated page spec.');
        }

        if (empty($updatedPageSpec->deprecatedThumbnailURL)) {
            throw new Exception('2: The `deprecatedThumbnailURL` property should be set on the updated page spec.');
        }

        $resultImageID = CBModel::value($updatedPageSpec, 'image.ID');
        $expectedImageID = CBTestAdmin::testImageID();

        if ($resultImageID !== $expectedImageID) {
            $resultImageIDAsJSON = json_encode($resultImageID);
            $expectedImageIDAsJSON = json_encode($expectedImageID);
            throw new Exception("2: The page spec \"image.ID\" property is {$resultImageIDAsJSON} but {$expectedImageIDAsJSON} was expected.");
        }

        // clean up

        CBModels::deleteByID($pageID);
        CBModels::deleteByID($resultImageID);

        return (object)[
            'succeeded' => true,
        ];
    }

    /**
     * @return [[<class>, <test>]]
     */
    static function CBUnitTests_tests(): array {
        return [
            ['CBPageVerificationTask', 'findDeprecatedSubviewClassNames'],
            ['CBPageVerificationTask', 'hasColbyPagesRow'],
            ['CBPageVerificationTask', 'importThumbnailURLToImage'],
            ['CBPageVerificationTask', 'invalidImageProperty'],
            ['CBPageVerificationTask', 'rowWithNoModel'],
            ['CBPageVerificationTask', 'upgradeThumbnailURLToImage'],
        ];
    }

    /**
     * @return ID
     */
    private static function createPagesRowAndDataStoreWithoutModel(): string {
        $ID = CBHex160::random();
        $now = time();

        $archiveIDAsSQL= CBHex160::toSQL($ID);
        $keyValueDataAsSQL = CBDB::stringToSQL('');
        $classNameAsSQL = 'NULL';
        $classNameForKindAsSQL = 'NULL';
        $createdAsSQL = $now;
        $iterationAsSQL = 1;
        $modifiedAsSQL = $now;
        $URIAsSQL = 'NULL';
        $thumbnailURLAsSQL = 'NULL';
        $searchTextAsSQL = 'NULL';
        $publishedAsSQL = 'NULL';
        $publishedByAsSQL = 'NULL';
        $publishedMonthAsSQL = 'NULL';

        CBDataStore::deleteByID($ID);
        CBPages::deletePagesByID([$ID]);

        $SQL = <<<EOT

            INSERT INTO ColbyPages
            VALUES (
                {$archiveIDAsSQL},
                {$keyValueDataAsSQL},
                {$classNameAsSQL},
                {$classNameForKindAsSQL},
                {$createdAsSQL},
                {$iterationAsSQL},
                {$modifiedAsSQL},
                {$URIAsSQL},
                {$thumbnailURLAsSQL},
                {$searchTextAsSQL},
                {$publishedAsSQL},
                {$publishedByAsSQL},
                {$publishedMonthAsSQL}
            )

EOT;

        Colby::query($SQL);

        if (!CBDB::SQLToValue("SELECT COUNT(*) FROM ColbyPages where archiveID = {$archiveIDAsSQL}")) {
            throw new RuntimeException('The ColbyPages row was not created.');
        }

        CBDataStore::create($ID);

        $filepath = CBDataStore::flexpath($ID, 'tmp.txt', cbsitedir());

        file_put_contents($filepath, __METHOD__ . "()\n");

        if (!is_dir(CBDataStore::directoryForID($ID)) || !is_file($filepath)) {
            throw new RuntimeException('The data store was not completely created.');
        }

        return $ID;
    }

    /**
     * @return model
     */
    private static function specWithDeprecatedAndUnsupportedViews(): stdClass {
        return (object)[
            'className' => 'CBViewPage',
            'title' => 'Test Page for CBTest_unsupportedViews() in CBPageVerificationTaskTests',
            'sections' => [
                (object)[
                    'className' => 'CBTextBoxView',
                ],
                (object)[
                    'className' => 'CBThemedTextView',
                ],
                (object)[
                    'className' => 'CBContainerView',
                    'subviews' => [
                        (object)[
                            'className' => 'CBImageView',
                        ],
                        (object)[
                            'className' => 'CBContainerView',
                            'subviews' => [
                                (object)[
                                    'className' => 'CBTextView',
                                ],
                                (object)[
                                    'className' => 'CBTextView2',
                                ],
                                (object)[
                                    'className' => 'CBImageView',
                                ],
                                (object)[
                                    'className' => 'CBMessageView',
                                ],
                                (object)[
                                    'className' => 'CBFlexBoxView',
                                ],
                            ]
                        ],
                    ],
                ],
            ],
        ];
    }
}
