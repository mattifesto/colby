<?php

final class CBPageSettingsCatalogTests {

    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'name' => 'test',
                'title' => 'CBPageSettingsCatalog',
                'type' => 'server',
            ],
        ];
    }



    /* -- tests -- -- -- -- -- */



    /**
     * @return object
     */
    static function CBTest_test(): stdClass {
        CBPageSettingsCatalog::$testID =
        '0fc48f5a3d1ee3322de14822184bb162e15efdbc';

        CBPageSettingsCatalog::CBInstall_install();

        CBPageSettingsCatalog::install(
            'CBPageSettingsCatalogTests_pageSettings1'
        );

        CBPageSettingsCatalog::install(
            'CBPageSettingsCatalogTests_pageSettings2'
        );

        CBPageSettingsCatalog::install(
            'CBPageSettingsCatalogTests_fake'
        );

        CBPageSettingsCatalog::install(
            'CBPageSettingsCatalogTests_pageSettings1'
        );

        CBPageSettingsCatalog::install(
            'CBPageSettingsCatalogTests_pageSettings2'
        );

        $pageSettingsClassNames = CBPageSettingsCatalog::fetchClassNames();

        $expectedClassNames = [
            'CBPageSettingsCatalogTests_pageSettings1',
            'CBPageSettingsCatalogTests_pageSettings2',
        ];

        if ($pageSettingsClassNames != $expectedClassNames) {
            return CBTest::resultMismatchFailure(
                'test 1',
                $pageSettingsClassNames,
                $expectedClassNames
            );
        }

        CBModels::deleteByID(CBPageSettingsCatalog::$testID);

        CBPageSettingsCatalog::$testID = null;

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_test() */

}



final class CBPageSettingsCatalogTests_pageSettings1 {
}



final class CBPageSettingsCatalogTests_pageSettings2 {
}
