<?php

final class CBPageKindCatalogTests {

    /**
     * @return object
     */
    static function CBTest_test(): stdClass {
        CBPageKindCatalog::$testID = 'fcbf0263351a639892a917e4d39d1e7ba05a9166';

        CBPageKindCatalog::CBInstall_install();
        CBPageKindCatalog::install('CBPageKindCatalogTests_pageKind1');
        CBPageKindCatalog::install('CBPageKindCatalogTests_pageKind2');
        CBPageKindCatalog::install('CBPageKindCatalogTests_fake');
        CBPageKindCatalog::install('CBPageKindCatalogTests_pageKind1');
        CBPageKindCatalog::install('CBPageKindCatalogTests_pageKind2');

        $pageKindClassNames = CBPageKindCatalog::fetchClassNames();
        $expectedClassNames = [
            'CBPageKindCatalogTests_pageKind1',
            'CBPageKindCatalogTests_pageKind2',
        ];

        if ($pageKindClassNames != $expectedClassNames) {
            return (object)[
                'message' =>
                    "The page kind class names do not match the expected class names\n\n" .
                    CBConvertTests::resultAndExpectedToMessage($pageKindClassNames, $expectedClassNames),
            ];
        }

        CBModels::deleteByID(CBPageKindCatalog::$testID);

        CBPageKindCatalog::$testID = null;

        return (object)[
            'succeeded' => true,
        ];
    }


    /**
     * @return [[<class>, <name>]]
     */
    static function CBUnitTests_tests(): array {
        return [
            ['CBPageKindCatalog', 'test']
        ];
    }
}

final class CBPageKindCatalogTests_pageKind1 {
}

final class CBPageKindCatalogTests_pageKind2 {
}
