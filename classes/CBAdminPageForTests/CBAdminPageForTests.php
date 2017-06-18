<?php

final class CBAdminPageForTests {

    /**
     * @deprecated use CBAdminPageForTests::testImageID() instead.
     *
     * The data store ID of the test image.
     */
    const imageID = '3dd8e721048bbe8ea5f0c043fab73277a0b0044c';

    /**
     * @return [string]
     */
    static function adminPageMenuNamePath() {
        return ['test', 'test'];
    }

    /**
     * @return stdClass
     */
    static function adminPagePermissions() {
        return (object)['group' => 'Developers'];
    }

    /**
     * @return null
     */
    static function adminPageRenderContent() {
        CBHTMLOutput::setTitleHTML('Tests');
        CBHTMLOutput::setDescriptionHTML('Run website unit tests.');
    }

    /**
     * The data store ID for the old image data store.
     *
     * @return hex160
     */
    static function oldStyleImageDataStoreID() {
        return 'acefdba4848ff407c150a240e2f177d59e3839b3';
    }

    /**
     * At one time images were uploaded into data store directories with no
     * mention in the database. This function creates a test data store similar
     * to those for use in testing upgrade scenarios.
     *
     * One you are done using this data store, remove it by calling:
     *
     *      CBAdminPageForTests::removeOldStyleImageDataStore();
     *
     * @return null
     */
    static function prepareOldStyleImageDataStore() {
        $ID = CBAdminPageForTests::oldStyleImageDataStoreID();
        $siteDirectory = CBSitePreferences::siteDirectory();

        CBDataStore::deleteByID($ID);
        CBDataStore::makeDirectoryForID($ID);

        $originalFilepath = CBAdminPageForTests::testImageFilepath();
        $largeFilepath = CBDataStore::flexpath($ID, 'large.jpeg', $siteDirectory);
        $mediumFilepath = CBDataStore::flexpath($ID, 'medium.jpeg', $siteDirectory);
        $thumbnailFilepath = CBDataStore::flexpath($ID, 'thumbnail.jpeg', $siteDirectory);

        copy($originalFilepath, $largeFilepath);

        $projection = CBProjection::fromImageFilepath($largeFilepath);
        $projection = CBProjection::applyOpString($projection, 'rw1280');
        CBImages::reduceImageFile($largeFilepath, $mediumFilepath, $projection);

        $projection = CBProjection::fromImageFilepath($largeFilepath);
        $projection = CBProjection::applyOpString($projection, 'rs400clc400');
        CBImages::reduceImageFile($largeFilepath, $thumbnailFilepath, $projection);

        // If this image has been imported, remove it.
        CBImages::deleteByID(CBAdminPageForTests::testImageID());
    }

    /**
     * @return null
     */
    static function removeOldStyleImageDataStore() {
        CBDataStore::deleteByID(CBAdminPageForTests::oldStyleImageDataStoreID());
        CBImages::deleteByID(CBAdminPageForTests::testImageID());
    }

    /**
     * @return [string]
     */
    static function requiredCSSURLs() {
        return [Colby::flexnameForCSSForClass(CBSystemURL, __CLASS__)];
    }

    /**
     * @return [string]
     */
    static function requiredJavaScriptURLs() {
        return [Colby::flexnameForJavaScriptForClass(CBSystemURL, __CLASS__)];
    }

    /**
     * This image can be used for testing purposes. Do not modify the original.
     *
     * @return string
     */
    static function testImageFilepath() {
        return __DIR__ . '/2017.02.02.TestImage.jpg';
    }

    /**
     * If the test image is imported as a CBImage this will be its image ID.
     *
     * @return hex160
     */
    static function testImageID() {
        return CBAdminPageForTests::imageID;
    }
}
