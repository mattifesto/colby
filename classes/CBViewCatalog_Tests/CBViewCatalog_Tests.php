<?php

final class CBViewCatalog_Tests {

    /* -- CBTest interfaces -- -- -- -- -- */

    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'type' => 'server',
                'title' => 'CBViewCatalog::installView()',
                'name' => 'installView',
            ],
        ];
    }
    /* CBTest_getTests() */


    /* -- tests -- -- -- -- -- */

    /**
     * @return object
     */
    static function CBTest_installView(): stdClass {
        $testID = '4f3d8e4ce9ef94db860660dfd5faf3e786c9915c';

        CBModels::deleteByID($testID);

        CBViewCatalog::$testID = $testID;

        try {

            /* test */

            $actualResult =
            CBViewCatalog::fetchSupportedViewClassNames();

            $expectedResult = [];

            if ($actualResult != $expectedResult) {
                return CBTest::resultMismatchFailure(
                    'initial supported view class names',
                    $actualResult,
                    $expectedResult
                );
            }

            /* test */

            CBViewCatalog::installView('FakeClassName');

            $actualResult =
            CBViewCatalog::fetchSupportedViewClassNames();

            $expectedResult = [];

            if ($actualResult != $expectedResult) {
                return CBTest::resultMismatchFailure(
                    'non-existent class name',
                    $actualResult,
                    $expectedResult
                );
            }

            /* test */

            CBViewCatalog::installView('CBArtworkView');

            $actualResult = [
                CBViewCatalog::fetchViewClassNames(),
                CBViewCatalog::fetchSupportedViewClassNames(),
                CBViewCatalog::fetchDeprecatedViewClassNames(),
                CBViewCatalog::fetchUnsupportedViewClassNames(),
            ];

            $expectedResult = [
                ['CBArtworkView'],
                ['CBArtworkView'],
                [],
                [],
            ];

            if ($actualResult != $expectedResult) {
                return CBTest::resultMismatchFailure(
                    'supported class name',
                    $actualResult,
                    $expectedResult
                );
            }

            /* test */

            CBViewCatalog::installView(
                'CBArtworkView',
                (object)[
                    'isDeprecated' => true,
                ]
            );

            $actualResult = [
                CBViewCatalog::fetchViewClassNames(),
                CBViewCatalog::fetchSupportedViewClassNames(),
                CBViewCatalog::fetchDeprecatedViewClassNames(),
                CBViewCatalog::fetchUnsupportedViewClassNames(),
            ];

            $expectedResult = [
                ['CBArtworkView'],
                [],
                ['CBArtworkView'],
                [],
            ];

            if ($actualResult != $expectedResult) {
                return CBTest::resultMismatchFailure(
                    'deprecated class name',
                    $actualResult,
                    $expectedResult
                );
            }

            /* test */

            CBViewCatalog::installView(
                'CBArtworkView',
                (object)[
                    'isUnsupported' => true,
                ]
            );

            $actualResult = [
                CBViewCatalog::fetchViewClassNames(),
                CBViewCatalog::fetchSupportedViewClassNames(),
                CBViewCatalog::fetchDeprecatedViewClassNames(),
                CBViewCatalog::fetchUnsupportedViewClassNames(),
            ];

            $expectedResult = [
                ['CBArtworkView'],
                [],
                [],
                ['CBArtworkView'],
            ];

            if ($actualResult != $expectedResult) {
                return CBTest::resultMismatchFailure(
                    'unsupported class name',
                    $actualResult,
                    $expectedResult
                );
            }
        } finally {
            CBViewCatalog::$testID = null;
            CBModels::deleteByID($testID);
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_installView() */
}
/* CBViewCatalog_Tests */
