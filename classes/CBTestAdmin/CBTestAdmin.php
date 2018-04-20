<?php

final class CBTestAdmin {

    /**
     * @return string
     */
    static function CBAdmin_group() {
        return 'Developers';
    }

    /**
     * @return [string]
     */
    static function CBAdmin_menuNamePath(): array {
        return ['develop', 'test'];
    }

    /**
     * @return void
     */
    static function CBAdmin_render(): void {
        CBHTMLOutput::pageInformation()->title = 'Test Administration';
    }

    /**
     * @return ID
     */
    static function CBAjax_testPagesRowAndDataStoreWithoutModel(): string {
        $ID = CBPageVerificationTaskTests::createPagesRowAndDataStoreWithoutModel();

        CBTasks2::restart('CBPageVerificationTask', $ID, 1);

        return $ID;
    }

    /**
     * @return string
     */
    static function CBAjax_testPagesRowAndDataStoreWithoutModel_group(): string {
        return 'Developers';
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [Colby::flexpath(__CLASS__, 'v374.css', cbsysurl())];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [Colby::flexpath(__CLASS__, 'v408.js', cbsysurl())];
    }

    /**
     * @return [[string (name), string (value)]]
     */
    static function CBHTMLOutput_JavaScriptVariables() {
        return [
            ['CBTestAdmin_javaScriptTests', CBTestAdmin::javaScriptTests()],
        ];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        $classNames = array_map(function ($element) {
            return $element[0] . "Tests";
        }, CBTestAdmin::javaScriptTests());

        $classNames[] = 'CBUI';
        $classNames[] = 'CBUIExpander';
        $classNames[] = 'CBUISectionItem4';
        $classNames[] = 'CBUIStringsPart';

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
            ['CBMessageMarkup', 'singleLineMarkupToText'],
            ['CBMessageMarkup', 'stringToMarkup'],
            ['Colby',           'centsToDollars'],
            ['Colby',           'dateToString'],
            ['Colby',           'random160'],
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
     * This image can be used for testing purposes. Do not modify the original.
     *
     * @return string
     */
    static function testImageFilepath() {
        return __DIR__ . '/2017.02.02.TestImage.jpg';
    }

    /**
     * @return string
     */
    static function testImageURL() {
        return cbsysurl() . '/classes/' . __CLASS__ . '/2017.02.02.TestImage.jpg';
    }

    /**
     * If the test image is imported as a CBImage this will be its image ID.
     *
     * @return hex160
     */
    static function testImageID() {
        return '3dd8e721048bbe8ea5f0c043fab73277a0b0044c';
    }
}
