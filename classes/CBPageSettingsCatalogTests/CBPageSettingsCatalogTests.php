<?php

final class CBPageSettingsCatalogTests {

    /**
     * @return object
     */
    static function CBTest_test(): stdClass {
        CBPageSettingsCatalog::$testID = '0fc48f5a3d1ee3322de14822184bb162e15efdbc';

        CBPageSettingsCatalog::CBInstall_install();
        CBPageSettingsCatalog::install('CBPageSettingsCatalogTests_pageSettings1');
        CBPageSettingsCatalog::install('CBPageSettingsCatalogTests_pageSettings2');
        CBPageSettingsCatalog::install('CBPageSettingsCatalogTests_fake');
        CBPageSettingsCatalog::install('CBPageSettingsCatalogTests_pageSettings1');
        CBPageSettingsCatalog::install('CBPageSettingsCatalogTests_pageSettings2');

        $pageSettingsClassNames = CBPageSettingsCatalog::fetchClassNames();
        $expectedClassNames = [
            'CBPageSettingsCatalogTests_pageSettings1',
            'CBPageSettingsCatalogTests_pageSettings2',
        ];

        if ($pageSettingsClassNames != $expectedClassNames) {
            return (object)[
                'message' =>
                    "The page settings class names do not match the expected class names\n\n" .
                    CBConvertTests::resultAndExpectedToMessage($pageSettingsClassNames, $expectedClassNames),
            ];
        }

        CBModels::deleteByID(CBPageSettingsCatalog::$testID);

        CBPageSettingsCatalog::$testID = null;

        return (object)[
            'succeeded' => true,
        ];
    }


    /**
     * @return [[<class>, <name>]]
     */
    static function CBUnitTests_tests(): array {
        return [
            ['CBPageSettingsCatalog', 'test']
        ];
    }
}

final class CBPageSettingsCatalogTests_pageSettings1 {
}

final class CBPageSettingsCatalogTests_pageSettings2 {
}
