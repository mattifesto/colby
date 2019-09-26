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


    /* -- CBHTMLOutput interfaces -- -- -- -- -- */

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
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v529.js', cbsysurl()),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */


    /**
     * @return [[string (name), string (value)]]
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
            'CBUISection',
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
