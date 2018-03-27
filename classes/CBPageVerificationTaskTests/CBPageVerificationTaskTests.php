<?php

final class CBPageVerificationTaskTests {

    /**
     * @return [[<class>, <test>]]
     */
    static function CBUnitTests_tests(): array {
        return [
            ['CBPageVerificationTask', 'hasColbyPagesRow'],
            ['CBPageVerificationTask', 'importThumbnailURLToImage'],
            ['CBPageVerificationTask', 'rowWithNoModel'],
            ['CBPageVerificationTask', 'upgradeThumbnailURLToImage'],
        ];
    }

    /**
     * @return ?object
     */
    static function hasColbyPagesRowTest(): ?stdClass {
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

        return null;
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

        CBTestAdmin::prepareOldStyleImageDataStore();

        Colby::query('START TRANSACTION');
        CBModels::deleteByID([$pageID]);
        Colby::query('COMMIT');

        $initialPageSspec = (object)[
            'isTest' => true,
            'className' => 'CBViewPage',
            'ID' => $pageID,
            'title' => 'Test Page for ' . __METHOD__ . '()',
            'thumbnailURL' => CBDataStore::flexpath(CBTestAdmin::oldStyleImageDataStoreID(), 'thumbnail.jpeg', CBSitePreferences::siteURL()),
        ];

        Colby::query('START TRANSACTION');
        CBModels::save([$initialPageSspec]);
        Colby::query('COMMIT');

        CBTasks2::runSpecificTask('CBPageVerificationTask', $pageID);

        $updatedPageSpec = CBModels::fetchSpecByID($pageID);

        if (!empty($updatedPageSpec->thumbnailURL)) {
            throw new Exception('The `thumbnailURL` property is still set on the updated page spec.');
        }

        if (empty($updatedPageSpec->deprecatedThumbnailURL)) {
            throw new Exception('The `deprecatedThumbnailURL` property should be set on the updated page spec.');
        }

        $imageID = CBModel::value($updatedPageSpec, 'image.ID');

        if ($imageID !== CBTestAdmin::testImageID()) {
            $v = json_encode($imageID);
            throw new Exception("The `image`.`ID` property has an incorrect value '{$v}' on the page spec.");
        }

        // clean up

        CBModels::deleteByID($pageID);
        CBTestAdmin::removeOldStyleImageDataStore();
    }

    /**
     * @return ?object
     */
    static function rowWithNoModelTest(): ?stdClass {
        $ID = 'ef58e8d3bf08afdf1c0f7f3d32478855a41aacc4';
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
            return (object)[
                'failed' => true,
                'message' =>
                    'The ColbyPages row should have been created.'
            ];
        }

        CBDataStore::create($ID);

        $filepath = CBDataStore::flexpath($ID, 'tmp.txt', cbsitedir());

        file_put_contents($filepath, __METHOD__ . "()\n");

        if (!is_dir(CBDataStore::directoryForID($ID))) {
            return (object)[
                'failed' => true,
                'message' =>
                    'The data store directory should have been created.'
            ];
        }

        $result = CBPageVerificationTask::run($ID);

        if (CBDB::SQLToValue("SELECT COUNT(*) FROM ColbyPages where archiveID = {$archiveIDAsSQL}")) {
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
     * @return null
     */
    static function upgradeThumbnailURLToImageTest() {
        $pageID = '4a7bc517a928056f9518d839881cc9f49ea10c0a';

        CBTestAdmin::prepareOldStyleImageDataStore();
        CBImages::importOldStyleImageDataStore(CBTestAdmin::oldStyleImageDataStoreID());

        Colby::query('START TRANSACTION');
        CBModels::deleteByID([$pageID]);
        Colby::query('COMMIT');

        $initialPageSspec = (object)[
            'isTest' => true,
            'className' => 'CBViewPage',
            'ID' => $pageID,
            'title' => 'Test Page for ' . __METHOD__ . '()',
            'thumbnailURL' => CBDataStore::flexpath(CBTestAdmin::testImageID(), 'rw640.jpeg', CBSitePreferences::siteURL()),
        ];

        Colby::query('START TRANSACTION');
        CBModels::save([$initialPageSspec]);
        Colby::query('COMMIT');

        CBTasks2::runSpecificTask('CBPageVerificationTask', $pageID);

        $updatedPageSpec = CBModels::fetchSpecByID($pageID);

        if (!empty($updatedPageSpec->thumbnailURL)) {
            throw new Exception('The `thumbnailURL` property is still set on the updated page spec.');
        }

        if (empty($updatedPageSpec->deprecatedThumbnailURL)) {
            throw new Exception('The `deprecatedThumbnailURL` property should be set on the updated page spec.');
        }

        $imageID = CBModel::value($updatedPageSpec, 'image.ID');

        if ($imageID !== CBTestAdmin::testImageID()) {
            $v = json_encode($imageID);
            throw new Exception("The `image`.`ID` property has an incorrect value '{$v}' on the page spec.");
        }

        // clean up

        CBModels::deleteByID($pageID);
        CBTestAdmin::removeOldStyleImageDataStore();
    }
}
