<?php

final class CBPageFrameCatalogTests {

    /**
     * @return object
     */
    static function CBTest_test(): stdClass {
        CBPageFrameCatalog::$testID = '961cf3538a564259b47db8658f5f507d92f63e52';

        CBPageFrameCatalog::CBInstall_install();
        CBPageFrameCatalog::install('CBPageFrameCatalogTests_frame1');
        CBPageFrameCatalog::install('CBPageFrameCatalogTests_frame2');
        CBPageFrameCatalog::install('CBPageFrameCatalogTests_fake');
        CBPageFrameCatalog::install('CBPageFrameCatalogTests_frame1');
        CBPageFrameCatalog::install('CBPageFrameCatalogTests_frame2');

        $frameClassNames = CBPageFrameCatalog::fetchClassNames();
        $expectedClassNames = [
            'CBPageFrameCatalogTests_frame1',
            'CBPageFrameCatalogTests_frame2',
        ];

        if ($frameClassNames != $expectedClassNames) {
            return (object)[
                'message' =>
                    "The frame class names do not match the expected class names\n\n" .
                    CBConvertTests::resultAndExpectedToMessage($frameClassNames, $expectedClassNames),
            ];
        }

        CBModels::deleteByID(CBPageFrameCatalog::$testID);

        CBPageFrameCatalog::$testID = null;

        return (object)[
            'succeeded' => true,
        ];
    }


    /**
     * @return [[<class>, <name>]]
     */
    static function CBUnitTests_tests(): array {
        return [
            ['CBPageFrameCatalog', 'test']
        ];
    }
}

final class CBPageFrameCatalogTests_frame1 {
    static function CBPageFrame_render(callable $renderContent): void {
        echo __CLASS__ . 'begin';
        $renderContent();
        echo __CLASS__ . 'end';
    }
}

final class CBPageFrameCatalogTests_frame2 {
    static function CBPageFrame_render(callable $renderContent): void {
        echo __CLASS__ . 'begin';
        $renderContent();
        echo __CLASS__ . 'end';
    }
}
