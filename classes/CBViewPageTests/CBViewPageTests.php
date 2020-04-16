<?php

final class CBViewPageTests {

    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'name' => 'general',
                'type' => 'server',
            ],
            (object)[
                'name' => 'save',
                'type' => 'server',
            ],
            (object)[
                'name' => 'upgrade',
                'type' => 'server',
            ],
        ];
    }
    /* CBTest_getTests() */



    /* -- tests -- -- -- -- -- */



    /**
     * @return object
     */
    static function CBTest_general(): stdClass {

        /* Test 1 */

        $spec = (object)[
            'className' => 'CBViewPage',
            'classNameForSettings' => 'CBViewPageTests_PageSettings',
            'image' => (object)[
                /* testing deprecated missing class name */
                'ID' => 'bf0c7e133bf1a4bd05a3490a6c05d8fa34f5833f',
                'base' => 'original',
                'extension' => 'jpeg',
                'height' => 700,
                'width' => 900,
            ],
            'publishedByUserCBID' => '2c40491a848286f63af308b78660227d025bed1b',
            'sections' => CBViewTests::testSubviewSpecs(),
        ];

        $expectedModel = (object)[
            'className' => 'CBViewPage',
            'classNameForKind' => '',
            'classNameForSettings' => 'CBViewPageTests_PageSettings',
            'description' => '',
            'frameClassName' => '',
            'isPublished' => false,
            'iteration' => 0,
            'selectedMenuItemNames' => [],
            'title' => '',
            'URI' => '',
            'publicationTimeStamp' => null,
            'publishedByUserCBID' => '2c40491a848286f63af308b78660227d025bed1b',
            'thumbnailURL' => '',
            'sections' => CBViewTests::testSubviewModels(),
            'thumbnailURLAsHTML' => '',
            'URIAsHTML' => '',
        ];

        $model = CBModel::build($spec);

        if ($model != $expectedModel) {
            return CBTest::resultMismatchFailure(
                'build',
                $model,
                $expectedModel
            );
        }

        /* Test 2 */

        $searchText = CBModel::toSearchText($model);
        $expectedSearchText = (
            CBViewTests::testSubviewSearchText() .
            ' CBViewPage'
        );

        if ($searchText !== $expectedSearchText) {
            return CBTest::resultMismatchFailure(
                'toSearchText',
                $searchText,
                $expectedSearchText
            );
        }


        /* done */

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_general() */



    /**
     * @return object
     */
    static function CBTest_save(): stdClass {
        $ID = '697f4e4cb46436f5c204e495caff5957d4d62a31';
        $kind = 'CBViewPageTestPages';
        $specURI = 'CBViewPageTests/super écali fragil isticø expialidociouså';
        $modelURI = 'cbviewpagetests/super-cali-fragil-istic-expialidocious';

        /**
         * @NOTE The building emoji will cause an error if the table is not
         * correctly updated. A heart emoji will not cause an error.
         */

        $title = 'I 🏛 <Websites>!';

        CBModels::deleteByID($ID);

        $spec = (object)[
            'className' => 'CBViewPage',
            'classNameForSettings' => 'CBPageSettingsForResponsivePages',
            'ID' => $ID,
            'title' => $title,
            'classNameForKind' => $kind,
            'isPublished' => true,
            'URI' => $specURI,
        ];

        CBModels::save($spec);


        /* test 1 */

        $SQL = <<<EOT

            SELECT  COUNT(*)
            FROM    ColbyPages
            WHERE   archiveID = UNHEX('{$ID}')

        EOT;

        $actualResult = CBDB::SQLToValue($SQL);
        $expectedResult = '1';

        if ($actualResult !== $expectedResult) {
            return CBTest::resultMismatchFailure(
                'test 1',
                $actualResult,
                $expectedResult
            );
        }


        /* test 2 */

        $SQL = <<<EOT

            SELECT  COUNT(*)
            FROM    ColbyPages
            WHERE   classNameForKind = '{$kind}'

        EOT;

        $actualResult = CBDB::SQLToValue($SQL);
        $expectedResult = '1';

        if ($actualResult !== $expectedResult) {
            return CBTest::resultMismatchFailure(
                'test 2',
                $actualResult,
                $expectedResult
            );
        }


        /* test 3 */

        $SQL = <<<EOT

            SELECT  URI
            FROM    ColbyPages
            WHERE   archiveID = UNHEX('{$ID}')

        EOT;

        $actualResult = CBDB::SQLToValue($SQL);

        if ($actualResult !== $modelURI) {
            return CBTest::resultMismatchFailure(
                'test 3',
                $actualResult,
                $modelURI
            );
        }


        /* delete test model */

        CBModels::deleteByID([$ID]);


        /* done */

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_save() */



    /**
     * @NOTE 2020_04_15
     *
     *      Upgrading numberic user IDs was removed from the CBViewPage class.
     *      These tests were modified only to remove that functionality. The
     *      tests were not checked otherwise.
     *
     * @return object
     */
    static function CBTest_upgrade(): stdClass {

        /* test 1 */

        $originalSpec = (object)[
            'className' => 'CBViewPage',

            'classNameForSettings' => 'CBViewPageTests_PageSettings',

            'image' => (object)[
                /* testing deprecated missing class name */
                'ID' => 'bf0c7e133bf1a4bd05a3490a6c05d8fa34f5833f',
                'base' => 'original',
                'extension' => 'jpeg',
                'height' => 700,
                'width' => 900,
            ],

            'publishedByUserCBID' => ColbyUser::getCurrentUserCBID(),
            'sections' => CBViewTests::testSubviewSpecs(),
        ];

        /* test 1 */

        $actualUpgradedSpec = CBModel::upgrade($originalSpec);

        $expectedUpgradedSpec = (object)[
            'className' => 'CBViewPage',
            'classNameForSettings' => 'CBViewPageTests_PageSettings',
            'image' => (object)[
                'className' => 'CBImage',
                'ID' => 'bf0c7e133bf1a4bd05a3490a6c05d8fa34f5833f',
                'filename' => 'original',
                'extension' => 'jpeg',
                'height' => 700,
                'width' => 900,
            ],
            'publishedByUserCBID' => ColbyUser::getCurrentUserCBID(),
            'sections' => CBViewTests::testSubviewUpgradedSpecs(),
        ];

        if ($actualUpgradedSpec != $expectedUpgradedSpec) {
            return CBTest::resultMismatchFailure(
                'test 1',
                $actualUpgradedSpec,
                $expectedUpgradedSpec
            );
        }


        /* test 2 */

        $originalSpec = (object)[
            'className' => 'CBViewPage',
            'classNameForSettings' => 'CBViewPageTests_PageSettings',
        ];

        $actualUpgradedSpec = CBModel::upgrade($originalSpec);

        $expectedUpgradedSpec = (object)[
            'className' => 'CBViewPage',
            'classNameForSettings' => 'CBViewPageTests_PageSettings',
            'sections' => [],
        ];

        if ($actualUpgradedSpec != $expectedUpgradedSpec) {
            return CBTest::resultMismatchFailure(
                'test 2',
                $actualUpgradedSpec,
                $expectedUpgradedSpec
            );
        }


        /* test 3 */

        $publishedByUserCBID = CBID::generateRandomCBID();

        $originalSpec = (object)[
            'className' => 'CBViewPage',
            'classNameForSettings' => 'CBViewPageTests_PageSettings',
            'publishedByUserCBID' => $publishedByUserCBID,
        ];

        $actualUpgradedSpec = CBModel::upgrade($originalSpec);

        $expectedUpgradedSpec = (object)[
            'className' => 'CBViewPage',
            'classNameForSettings' => 'CBViewPageTests_PageSettings',
            'publishedByUserCBID' => $publishedByUserCBID,
            'sections' => [],
        ];

        if ($actualUpgradedSpec != $expectedUpgradedSpec) {
            return CBTest::resultMismatchFailure(
                'test 3',
                $actualUpgradedSpec,
                $expectedUpgradedSpec
            );
        }


        /* done */

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_upgrade() */

}
