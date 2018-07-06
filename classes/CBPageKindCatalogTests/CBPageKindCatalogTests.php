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

        $actualClassNames = CBPageKindCatalog::fetchClassNames();
        $expectedClassNames = [
            'CBPageKindCatalogTests_pageKind1',
            'CBPageKindCatalogTests_pageKind2',
        ];

        CBModels::deleteByID(CBPageKindCatalog::$testID);

        CBPageKindCatalog::$testID = null;

        if ($actualClassNames != $expectedClassNames) {
            return CBTest::resultMismatchFailure('subtest1', $actualClassNames, $expectedClassNames);
        }

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
