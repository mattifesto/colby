<?php

final class CBPageVerificationTaskTests {

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
