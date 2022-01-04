<?php

final class
CBPageFrameCatalogTests {

    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function
    CBTest_getTests(
    ): array {
        return [
            (object)[
                'name' => 'test',
                'type' => 'server',
            ],
        ];
    }
    /* CBTest_getTests() */



    /* -- tests -- -- -- -- -- */



    /**
     * @return object
     */
    static function
    test(
    ): stdClass {
        CBPageFrameCatalog::$testID = (
            '961cf3538a564259b47db8658f5f507d92f63e52'
        );

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

        if (
            $frameClassNames != $expectedClassNames
        ) {
            return CBTest::resultMismatchFailure(
                'test 1',
                $frameClassNames,
                $expectedClassNames
            );
        }

        CBDB::transaction(
            function () {
                CBModels::deleteByID(
                    CBPageFrameCatalog::$testID
                );
            }
        );

        CBPageFrameCatalog::$testID = null;

        return (object)[
            'succeeded' => true,
        ];
    }
    /* test() */

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
