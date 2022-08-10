<?php

final class CBTestAdmin {

    /* -- CBAdmin interfaces -- -- -- -- -- */



    /**
     * @return string
     */
    static function CBAdmin_getUserGroupClassName() {
        return 'CBDevelopersUserGroup';
    }



    /**
     * @return [string]
     */
    static function CBAdmin_menuNamePath(): array {
        return [
            'develop',
            'test',
        ];
    }



    /**
     * @return void
     */
    static function CBAdmin_render(): void {
        CBHTMLOutput::pageInformation()->title = 'Test Administration';
    }



    // -- CBHTMLOutput interfaces



    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v374.css', cbsysurl()),
        ];
    }
    /* CBHTMLOutput_CSSURLs() */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array
    {
        $javaScriptURLs =
        [
            CBLibrary::buildLibraryClassFilePath(
                __CLASS__,
                '2022_08_10_1660090897',
                'js',
                cbsysurl()
            ),
        ];

        return $javaScriptURLs;
    }
    // CBHTMLOutput_JavaScriptURLs()



    /**
     * @return [[<name>, <value>]]
     */
    static function CBHTMLOutput_JavaScriptVariables() {
        return [
            [
                'CBTestAdmin_tests',
                CBTest::getTests(),
            ],
        ];
    }
    /* CBHTMLOutput_JavaScriptVariables() */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        $classNames = [
            'CBConvert',
            'CBException',
            'CBMessageMarkup',
            'CBModel',
            'CBTest',
            'CBUI',
            'CBUIExpander',
            'CBUINavigationView',
            'CBUIPanel',
            'CBUISectionItem4',
            'CBUISelector',
            'CBUIStringsPart',
            'Colby',
        ];

        return array_values(array_unique(array_merge(
            $classNames,

            array_map(
                function (stdClass $test) {
                    return CBModel::valueToString($test, "testClassName");
                },
                CBTest::getTests()
            )
        )));
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $updater = CBModelUpdater::fetch(
            (object)[
                'ID' => CBDevelopAdminMenu::getModelCBID(),
            ]
        );

        $items = CBModel::valueToArray(
            $updater->working,
            'items'
        );

        array_push(
            $items,
            (object)[
                'className' => 'CBMenuItem',
                'name' => 'test',
                'text' => 'Test',
                'URL' => CBAdmin::getAdminPageURL(
                    'CBTestAdmin'
                ),
            ]
        );

        $updater->working->items = $items;

        CBModelUpdater::save($updater);
    }
    /* CBInstall_install() */



    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBDevelopAdminMenu',
        ];
    }



    /* -- functions -- -- -- -- -- */



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
     * @return ID
     */
    static function testImageID() {
        return '3dd8e721048bbe8ea5f0c043fab73277a0b0044c';
    }

}
/* CBTestAdmin */
