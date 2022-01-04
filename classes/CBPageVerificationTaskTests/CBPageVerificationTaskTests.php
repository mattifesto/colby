<?php

final class
CBPageVerificationTaskTests {

    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function
    CBTest_getTests(
    ): array {
        return [
            (object)[
                'name' => 'deprecatedAndUnsupportedViews',
                'type' => 'server',
            ],
            (object)[
                'name' => 'findDeprecatedSubviewClassNames',
                'type' => 'server',
            ],
            (object)[
                'name' => 'findUnsupportedSubviewClassNames',
                'type' => 'server',
            ],
            (object)[
                'name' => 'hasColbyPagesRow',
                'type' => 'server',
            ],
            (object)[
                'name' => 'importThumbnailURLToImage',
                'type' => 'server',
            ],
            (object)[
                'name' => 'invalidImageProperty',
                'type' => 'server',
            ],
            (object)[
                'name' => 'rowWithNoModel',
                'type' => 'server',
            ],
            (object)[
                'name' => 'upgradeThumbnailURLToImage',
                'type' => 'server',
            ],
        ];
    }
    /* CBTest_getTests() */



    /* -- tests -- -- -- -- -- */



    /**
     * @return object
     */
    static function
    CBTest_deprecatedAndUnsupportedViews(
    ): stdClass {
        $ID = 'f9553b44249935fb78965c67862a1cec675b0835';

        $spec = (
            CBPageVerificationTaskTests::specWithDeprecatedAndUnsupportedViews()
        );

        CBModel::setCBID(
            $spec,
            $ID
        );

        CBViewPage::setURI(
            $spec,
            $ID
        );

        CBDB::transaction(
            function () use ($ID) {
                CBModels::deleteByID($ID);
            }
        );

        /**
         * Save the test model. Saving performs an upgrade which will produce
         * log entries which we don't want saved in the log so buffer and
         * dispose of them.
         */

        {
            CBLog::bufferStart();

            CBDB::transaction(
                function () use ($spec) {
                    CBModels::save($spec);
                }
            );

            CBLog::bufferEndClean();
        }


        CBLog::bufferStart();

        CBTasks2::runSpecificTask(
            'CBPageVerificationTask',
            $ID
        );

        $entries = CBLog::bufferContents();

        CBLog::bufferEndClean();

        CBDB::transaction(
            function () use ($ID) {
                CBModels::deleteByID($ID);
            }
        );

        $actualSourceIDs = array_map(
            function ($entry) {
                return CBModel::valueAsID($entry, 'sourceID');
            },
            $entries
        );

        $expectedSourceIDs = [
            '06232e21d9ced6f7b8f91fb0f7ae381944e5f4f2',
            'd8faccb5fe6161d7a61a12ddcdbc5b16f42c6e4d',
            'd05773c6b25805a051444e411221e7ed585d45b3',
            '79ea4dab030cff53f132622f6309bc44b552908a',
        ];

        if ($actualSourceIDs != $expectedSourceIDs) {
            return CBTest::resultMismatchFailure(
                'Subtest 1',
                $actualSourceIDs,
                $expectedSourceIDs
            );
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_deprecatedAndUnsupportedViews() */



    /**
     * @return object
     */
    static function CBTest_findDeprecatedSubviewClassNames(): stdClass {
        $spec = (
            CBPageVerificationTaskTests::specWithDeprecatedAndUnsupportedViews()
        );

        $expectedDeprecatedSubviewClassNames = [
            'CBThemedTextView',
            'CBTextView2',
        ];

        $actualDeprecatedSubviewClassNames =
            CBPageVerificationTask::findDeprecatedSubviewClassNames($spec);

        if (
            $actualDeprecatedSubviewClassNames !=
            $expectedDeprecatedSubviewClassNames
        ) {
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
    /* CBTest_findDeprecatedSubviewClassNames() */



    /**
     * @return object
     */
    static function CBTest_findUnsupportedSubviewClassNames(): stdClass {
        $spec = (
            CBPageVerificationTaskTests::specWithDeprecatedAndUnsupportedViews()
        );

        $expectedUnsupportedSubviewClassNames = [
            'CBTextBoxView',
            'CBImageView',
            'CBTextView',
            'CBImageView',
            'CBFlexBoxView',
        ];

        $actualUnsupportedSubviewClassNames =
            CBPageVerificationTask::findUnsupportedSubviewClassNames($spec);

        if (
            $actualUnsupportedSubviewClassNames !=
            $expectedUnsupportedSubviewClassNames
        ) {
            return CBTest::resultMismatchFailure(
                'Subtest 1',
                $actualUnsupportedSubviewClassNames,
                $expectedUnsupportedSubviewClassNames
            );
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_findUnsupportedSubviewClassNames() */



    /**
     * @return object
     */
    static function
    CBTest_hasColbyPagesRow(
    ): stdClass {
        CBLog::bufferStart();

        $ID = '722880dadcef3874157d086b4eceeae83194173f';

        $spec = (object)[
            'className' => 'CBViewPage',
            'classNameForSettings' => 'CBPageSettingsForResponsivePages',
            'ID' => $ID,
            'title' => 'Test Page For ' . __METHOD__ . '()',
        ];

        CBViewPage::setURI(
            $spec,
            $ID
        );

        CBDB::transaction(
            function () use ($ID) {
                CBModels::deleteByID($ID);
            }
        );

        $result = CBPageVerificationTask::run(
            $ID
        );

        $actual = $result->hasColbyPagesRow;
        $expected = false;

        if ($actual !== $expected) {
            return CBTest::resultMismatchFailure(
                'test 1',
                $actual,
                $expected
            );
        }

        CBDB::transaction(
            function () use ($spec) {
                CBModels::save($spec);
            }
        );

        $result = CBPageVerificationTask::run(
            $ID
        );

        $actual = $result->hasColbyPagesRow;
        $expected = true;

        if ($actual !== $expected) {
            return CBTest::resultMismatchFailure(
                'test 2',
                $actual,
                $expected
            );
        }

        CBDB::transaction(
            function () use ($ID) {
                CBModels::deleteByID($ID);
            }
        );


        /* test */

        $testName = 'log entries';
        $actualLogEntries = CBLog::bufferContents();

        CBLog::bufferEndClean();

        $expectedLogEntries = [];

        if ($actualLogEntries != $expectedLogEntries) {
            return CBTest::resultMismatchFailure(
                $testName,
                $actualLogEntries,
                $expectedLogEntries
            );
        }


        /* done */

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_hasColbyPagesRow() */



    /**
     * This test creates a page with a `thumbnailURL` referencing an old style
     * image data store and then runs CBPageVerificationTask on it to make sure
     * that the image is imported and the spec is upgraded to use the `image`
     * property instead of `thumbnailURL`.
     *
     * @return object
     */
    static function
    importThumbnailURLToImage(
    ): stdClass {
        $pageID = '4a7bc517a928056f9518d839881cc9f49ea10c0a';
        $temporaryImageDataStoreID = 'a66a45225d071a4f6e65c475ece1810ac4dec45a';

        CBDB::transaction(
            function () use (
                $pageID
            ) {
                CBModels::deleteByID(
                    $pageID
                );
            }
        );

        CBDB::transaction(
            function () use (
                $temporaryImageDataStoreID
            ) {
                CBModels::deleteByID(
                    $temporaryImageDataStoreID
                );
            }
        );

        CBDB::transaction(
            function () {
                CBModels::deleteByID(
                    CBTestAdmin::testImageID()
                );
            }
        );

        $testImageFilepath = CBTestAdmin::testImageFilepath();

        $temporaryImageFilepath = CBDataStore::flexpath(
            $temporaryImageDataStoreID,
            'test.jpeg',
            cbsitedir()
        );

        $temporaryImageURL = CBDataStore::flexpath(
            $temporaryImageDataStoreID,
            'test.jpeg',
            cbsiteurl()
        );

        CBDataStore::create(
            $temporaryImageDataStoreID
        );

        copy(
            $testImageFilepath,
            $temporaryImageFilepath
        );

        $initialPageSpec = CBModelTemplateCatalog::fetchLivePageTemplate(
            (object)[
                'isTest' => true,
                'ID' => $pageID,
                'title' => 'Test Page for ' . __METHOD__ . '()',
                'thumbnailURL' => $temporaryImageURL,
            ]
        );

        CBViewPage::setURI(
            $initialPageSpec,
            $pageID
        );

        CBDB::transaction(
            function () use (
                $initialPageSpec
            ) {
                CBModels::save(
                    $initialPageSpec
                );
            }
        );

        CBLog::bufferStart();

        CBTasks2::runSpecificTask(
            'CBPageVerificationTask',
            $pageID
        );

        $entries = CBLog::bufferContents();

        CBLog::bufferEndClean();

        /* log entry count */

        $actual = count(
            $entries
        );

        $expected = 3;

        if (
            $actual !== $expected
        ) {
            return CBTest::resultMismatchFailure(
                'Log entry count',
                $actual,
                $expected
            );
        }

        /* log entry source ID */

        $actual = CBModel::valueAsID(
            $entries[0],
            'sourceID'
        );

        $expected = '0099cecb597038d4bf5f182965271e25cc60c070';

        if (
            $actual !== $expected
        ) {
            return CBTest::resultMismatchFailure(
                'Log entry source ID',
                $actual,
                $expected
            );
        }

        /* --- */

        $updatedPageSpec = CBModels::fetchSpecByID(
            $pageID
        );

        if (
            !empty($updatedPageSpec->thumbnailURL)
        ) {
            throw new Exception(
                'The `thumbnailURL` property is still set on the updated ' .
                'page spec.'
            );
        }

        if (
            empty($updatedPageSpec->deprecatedThumbnailURL)
        ) {
            throw new Exception(
                'The `deprecatedThumbnailURL` property should be set on ' .
                'the updated page spec.'
            );
        }

        $resultImageID = CBModel::value(
            $updatedPageSpec,
            'image.ID'
        );

        $expectedImageID = CBTestAdmin::testImageID();

        if (
            $resultImageID !== $expectedImageID
        ) {
            $resultImageIDAsJSON = json_encode($resultImageID);
            $expectedImageIDAsJSON = json_encode($expectedImageID);

            throw new Exception(
                "1: The page spec \"image.ID\" property is " .
                "{$resultImageIDAsJSON} but {$expectedImageIDAsJSON} " .
                "was expected."
            );
        }

        // clean up

        CBDB::transaction(
            function () use (
                $pageID
            ) {
                CBModels::deleteByID(
                    $pageID
                );
            }
        );

        CBDB::transaction(
            function () use (
                $temporaryImageDataStoreID
            ) {
                CBModels::deleteByID(
                    $temporaryImageDataStoreID
                );
            }
        );

        CBDB::transaction(
            function () {
                CBModels::deleteByID(
                    CBTestAdmin::testImageID()
                );
            }
        );

        return (object)[
            'succeeded' => true,
        ];
    }
    /* importThumbnailURLToImage() */



    /**
     * @return object
     */
    static function
    CBTest_invalidImageProperty(
    ): stdClass {
        $ID = '5cf7dc1d21b1c70d62eeede0b9558d63f91781a3';

        $spec = (object)[
            'className' => 'CBViewPage',
            'classNameForSettings' => 'CBPageSettingsForResponsivePages',
            'ID' => $ID,
            'image' => (
                'This test image property value is a string. A valid ' .
                'property value would be a CBImage spec.'
            ),
            'title' => (
                'Test Page for CBTest_invalidImageProperty() in ' .
                'CBPageVerificationTaskTests'
            ),
        ];

        CBViewPage::setURI(
            $spec,
            $ID
        );

        CBDB::transaction(
            function () use ($ID) {
                CBModels::deleteByID($ID);
            }
        );

        CBDB::transaction(
            function () use ($spec) {
                CBModels::save($spec);
            }
        );

        CBLog::bufferStart();

        $taskWasRun = CBTasks2::runSpecificTask(
            'CBPageVerificationTask',
            $ID
        );

        $entries = CBLog::bufferContents();

        CBLog::bufferEndClean();

        /* task was run */

        $actual = $taskWasRun;
        $expected = true;

        if ($actual !== $expected) {
            return CBTest::resultMismatchFailure(
                'Task was run',
                $actual,
                $expected
            );
        }

        /* log entry count */

        $actual = count($entries);
        $expected = 3;

        if ($actual !== $expected) {
            return CBTest::resultMismatchFailure(
                'Log entry count',
                $actual,
                $expected
            );
        }

        /* log entry source ID */

        $actual = CBModel::valueAsID($entries[0], 'sourceID');
        $expected = '4b30866e7e5e5edf42b7a5cab882b072ec144b75';

        if ($actual !== $expected) {
            return CBTest::resultMismatchFailure(
                'Log entry source ID',
                $actual,
                $expected
            );
        }

        CBDB::transaction(
            function () use ($ID) {
                CBModels::deleteByID($ID);
            }
        );

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_invalidImageProperty() */



    /**
     * @return ?object
     */
    static function CBTest_rowWithNoModel(): stdClass {
        $ID = (
            CBPageVerificationTaskTests::createPagesRowAndDataStoreWithoutModel()
        );

        $IDAsSQL = CBID::toSQL($ID);
        $result = CBPageVerificationTask::run($ID);

        if (
            CBDB::SQLToValue(
                "SELECT COUNT(*) FROM ColbyPages where archiveID = {$IDAsSQL}"
            )
        ) {
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

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_rowWithNoModel() */



    /**
     * This test creates a page that has a `thumbnailURL` that references an
     * image from a CBImage. The test then runs CBPageVerificationTask on the
     * page to make sure the `thumbnailURL` is upgraded to an `image` for the
     * same CBImage.
     *
     * @return object
     */
    static function
    upgradeThumbnailURLToImage(
    ): stdClass {
        $pageID = 'e87c8eef4953d3060faaa2e3597c730326adfc29';

        CBDB::transaction(
            function () use (
                $pageID
            ) {
                CBModels::deleteByID(
                    $pageID
                );
            }
        );

        CBDB::transaction(
            function () {
                CBModels::deleteByID(
                    CBTestAdmin::testImageID()
                );
            }
        );

        $testImage = CBImages::URIToCBImage(
            CBTestAdmin::testImageFilepath()
        );

        if (
            $testImage->ID !== CBTestAdmin::testImageID()
        ) {
            throw new Exception(
                '2: The imported test image ID is not what was expected.'
            );
        }

        $testImageURL = CBDataStore::flexpath(
            $testImage->ID,
            'rw640.jpeg',
            cbsiteurl()
        );

        $initialPageSpec = CBModelTemplateCatalog::fetchLivePageTemplate(
            (object)[
                'isTest' => true,
                'ID' => $pageID,
                'title' => 'Test Page for ' . __METHOD__ . '()',
                'thumbnailURL' => $testImageURL,
            ]
        );

        CBViewPage::setURI(
            $initialPageSpec,
            $pageID
        );

        CBDB::transaction(
            function () use (
                $initialPageSpec
            ) {
                CBModels::save(
                    $initialPageSpec
                );
            }
        );

        CBLog::bufferStart();

        CBTasks2::runSpecificTask(
            'CBPageVerificationTask',
            $pageID
        );

        $buffer = CBLog::bufferContents();

        CBLog::bufferEndClean();

        $bufferIsValid = function (
            $buffer
        ): bool {
            if (
                count($buffer) !== 3
            ) {
                return false;
            }

            $sourceID = CBModel::valueAsID(
                $buffer[0],
                'sourceID'
            );

            if (
                $sourceID !== '0099cecb597038d4bf5f182965271e25cc60c070'
            ) {
                return false;
            }

            return true;
        };

        if (
            !$bufferIsValid($buffer)
        ) {
            $bufferAsMessage = CBMessageMarkup::stringToMessage(
                CBConvert::valueToPrettyJSON(
                    $buffer
                )
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

        $updatedPageSpec = CBModels::fetchSpecByID(
            $pageID
        );

        if (
            !empty($updatedPageSpec->thumbnailURL)
        ) {
            throw new Exception(
                '2: The `thumbnailURL` property is still set on the ' .
                'updated page spec.'
            );
        }

        if (
            empty($updatedPageSpec->deprecatedThumbnailURL)
        ) {
            throw new Exception(
                '2: The `deprecatedThumbnailURL` property should be set ' .
                'on the updated page spec.'
            );
        }

        $resultImageID = CBModel::value(
            $updatedPageSpec,
            'image.ID'
        );

        $expectedImageID = CBTestAdmin::testImageID();

        if (
            $resultImageID !== $expectedImageID
        ) {
            $resultImageIDAsJSON = json_encode(
                $resultImageID
            );

            $expectedImageIDAsJSON = json_encode(
                $expectedImageID
            );

            throw new Exception(
                "2: The page spec \"image.ID\" property is " .
                "{$resultImageIDAsJSON} but {$expectedImageIDAsJSON} " .
                "was expected."
            );
        }

        // clean up

        CBDB::transaction(
            function () use (
                $pageID
            ) {
                CBModels::deleteByID(
                    $pageID
                );
            }
        );

        CBDB::transaction(
            function () use (
                $resultImageID
            ) {
                CBModels::deleteByID(
                    $resultImageID
                );
            }
        );

        return (object)[
            'succeeded' => true,
        ];
    }
    /* upgradeThumbnailURLToImage() */



    /* -- functions -- -- -- -- -- */



    /**
     * @return CBID
     */
    private static function createPagesRowAndDataStoreWithoutModel(): string {
        $ID = CBID::generateRandomCBID();
        $now = time();

        $archiveIDAsSQL= CBID::toSQL($ID);
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
                {$publishedMonthAsSQL}
            )

        EOT;

        Colby::query($SQL);

        if (
            !CBDB::SQLToValue(
                "SELECT COUNT(*) FROM ColbyPages " .
                "where archiveID = {$archiveIDAsSQL}"
            )
        ) {
            throw new RuntimeException(
                'The ColbyPages row was not created.'
            );
        }

        CBDataStore::create($ID);

        $filepath = CBDataStore::flexpath($ID, 'tmp.txt', cbsitedir());

        file_put_contents($filepath, __METHOD__ . "()\n");

        if (
            !is_dir(CBDataStore::directoryForID($ID)) ||
            !is_file($filepath)
        ) {
            throw new RuntimeException(
                'The data store was not completely created.'
            );
        }

        return $ID;
    }
    /* createPagesRowAndDataStoreWithoutModel() */



    /**
     * @return object
     */
    private static function
    specWithDeprecatedAndUnsupportedViews(
    ): stdClass {
        return (object)[
            'className' => 'CBViewPage',
            'classNameForSettings' => 'CBPageSettingsForResponsivePages',
            'title' => (
                'Test Page for CBTest_unsupportedViews() in ' .
                'CBPageVerificationTaskTests'
            ),
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
    /* specWithDeprecatedAndUnsupportedViews() */

}
/* CBPageVerificationTaskTests */
