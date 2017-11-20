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
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [Colby::flexpath(__CLASS__, 'css', cbsysurl())];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [Colby::flexpath(__CLASS__, 'js', cbsysurl())];
    }

    /**
     * @return [[string (name), string (value)]]
     */
    static function CBHTMLOutput_JavaScriptVariables() {
        return [
            ['CBAdminPageForTests_javaScriptTests', CBAdminPageForTests::javaScriptTests()],
        ];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        $classNames = array_map(function ($element) {
            return $element[0] . "Tests";
        }, CBAdminPageForTests::javaScriptTests());

        $classNames[] = 'CBUI';

        return array_values(array_unique($classNames));
    }

    /**
     * @return [[string (className), string (testName)]]
     */
    static function javaScriptTests(): array {
        return [
            ['CBImages',        'deleteByID'],
            ['CBImages',        'upload'],
            ['CBMessageMarkup', 'markupToHTML'],
            ['CBMessageMarkup', 'markupToText'],
            ['Colby',           'centsToDollars'],
            ['Colby',           'dateToString'],
        ];
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
        CBAdminPageForTests::removeOldStyleImageDataStore();

        $ID = CBAdminPageForTests::oldStyleImageDataStoreID();

        CBDataStore::makeDirectoryForID($ID);

        $originalFilepath = CBAdminPageForTests::testImageFilepath();
        $largeFilepath = CBDataStore::flexpath($ID, 'large.jpeg', cbsitedir());
        $mediumFilepath = CBDataStore::flexpath($ID, 'medium.jpeg', cbsitedir());
        $thumbnailFilepath = CBDataStore::flexpath($ID, 'thumbnail.jpeg', cbsitedir());

        copy($originalFilepath, $largeFilepath);

        $projection = CBProjection::fromImageFilepath($largeFilepath);
        $projection = CBProjection::applyOpString($projection, 'rw1280');
        CBImages::reduceImageFile($largeFilepath, $mediumFilepath, $projection);

        $projection = CBProjection::fromImageFilepath($largeFilepath);
        $projection = CBProjection::applyOpString($projection, 'rs400clc400');
        CBImages::reduceImageFile($largeFilepath, $thumbnailFilepath, $projection);
    }

    /**
     * @return null
     */
    static function removeOldStyleImageDataStore() {
        CBModels::deleteByID(CBAdminPageForTests::oldStyleImageDataStoreID());
        CBModels::deleteByID(CBAdminPageForTests::testImageID());
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
