<?php

final class
CBViewPageTests {

    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'name' => 'CBViewPage_getFrameSearchText',
                'type' => 'server',
            ],
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
            (object)[
                'name' => 'upgradeSelectedMainMenuItemName',
                'type' => 'server',
            ],
            (object)[
                'name' => 'upgradeURI',
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
    CBViewPage_getFrameSearchText(
    ): stdClass {
        $testCaseModels = [

            (object)[
                'viewPageModel' => (object)[
                    'className' => 'CBViewPage',
                ],
                'expectedSearchText' => '',
            ],

            (object)[
                'viewPageModel' => (object)[
                    'className' => 'CBViewPage',
                    'frameClassName' => 'CBViewPageTests_PageFrame',
                ],
                'expectedSearchText' => CBConvert::stringToCleanLine(<<<EOT

                    CBViewPageTests_PageFrame frame search text

                EOT),
            ],

            (object)[
                'viewPageModel' => (object)[
                    'className' => 'CBViewPage',
                    'frameClassName' => 'CBViewPageTests_PageFrame',
                    'layout' => (object)[
                        'className' => 'CBViewPageTests_PageLayout',
                    ],
                ],
                'expectedSearchText' => CBConvert::stringToCleanLine(<<<EOT

                    CBViewPageTests_PageFrame frame search text

                EOT),
            ],

            (object)[
                'viewPageModel' => (object)[
                    'className' => 'CBViewPage',
                    'layout' => (object)[
                        'className' => 'CBViewPageTests_PageLayout',
                    ],
                ],
                'expectedSearchText' => '',
            ],

        ];

        for (
            $index = 0;
            $index < count ($testCaseModels);
            $index += 1
        ) {
            $currentTestCaseModel = $testCaseModels[$index];

            $currentViewPageModel = CBModel::valueAsModel(
                $currentTestCaseModel,
                'viewPageModel'
            );

            $actualSearchText = CBModel::toSearchText(
                $currentViewPageModel
            );

            $expectedSearchText = CBModel::valueToString(
                $currentTestCaseModel,
                'expectedSearchText'
            );

            if ($actualSearchText !== $expectedSearchText) {
                return CBTest::resultMismatchFailure(
                    "text case model index {$index}",
                    $actualSearchText,
                    $expectedSearchText
                );
            }
        }

        /* done */

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_CBViewPage_getFrameSearchText() */



    /**
     * @return object
     */
    static function
    general(
    ): stdClass {

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
            'URI' => '',
            'URIAsHTML' => '',
        ];

        $model = CBModel::build(
            $spec
        );

        if (
            $model != $expectedModel
        ) {
            return CBTest::resultMismatchFailure(
                'build',
                $model,
                $expectedModel
            );
        }

        /* Test 2 */

        $searchText = CBModel::toSearchText(
            $model
        );

        $expectedSearchText = CBViewTests::testSubviewSearchText();

        if (
            $searchText !== $expectedSearchText
        ) {
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
    static function
    CBTest_save(
    ): stdClass {
        $ID = '697f4e4cb46436f5c204e495caff5957d4d62a31';
        $kind = 'CBViewPageTestPages';
        $specURI = 'cbviewpagetests/super-cali-fragil-istic-expialidocious';
        $modelURI = 'cbviewpagetests/super-cali-fragil-istic-expialidocious';

        /**
         * @NOTE The building emoji will cause an error if the table is not
         * correctly updated. A heart emoji will not cause an error.
         */

        $title = 'I 🏛 <Websites>!';

        CBDB::transaction(
            function () use (
                $ID
            ) {
                CBModels::deleteByID($ID);
            }
        );

        $spec = (object)[
            'className' => 'CBViewPage',
            'classNameForSettings' => 'CBPageSettingsForResponsivePages',
            'ID' => $ID,
            'title' => $title,
            'classNameForKind' => $kind,
            'isPublished' => true,
        ];

        CBViewPage::setURI(
            $spec,
            $specURI
        );

        CBDB::transaction(
            function () use (
                $spec
            ) {
                CBModels::save(
                    $spec
                );
            }
        );


        /* test 1 */

        $SQL = <<<EOT

            SELECT  COUNT(*)
            FROM    ColbyPages
            WHERE   archiveID = UNHEX('{$ID}')

        EOT;

        $actualResult = CBDB::SQLToValue(
            $SQL
        );

        $expectedResult = '1';

        if (
            $actualResult !== $expectedResult
        ) {
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

        $actualResult = CBDB::SQLToValue(
            $SQL
        );

        $expectedResult = '1';

        if (
            $actualResult !== $expectedResult
        ) {
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

        $actualResult = CBDB::SQLToValue(
            $SQL
        );

        if ($actualResult !== $modelURI) {
            return CBTest::resultMismatchFailure(
                'test 3',
                $actualResult,
                $modelURI
            );
        }


        /* delete test model */

        CBDB::transaction(
            function () use (
                $ID
            ) {
                CBModels::deleteByID($ID);
            }
        );


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
    static function
    upgrade(
    ): stdClass {

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
            'URI' => '',
            'CBViewPage_versionDate' => '2020_11_11',
            'CBModel_versionDate_property' => '2022_01_15',
        ];

        if (
            $actualUpgradedSpec != $expectedUpgradedSpec
        ) {
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
            'URI' => '',
            'CBViewPage_versionDate' => '2020_11_11',
            'CBModel_versionDate_property' => '2022_01_15',
        ];

        if (
            $actualUpgradedSpec != $expectedUpgradedSpec
        ) {
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
            'URI' => '',
            'CBViewPage_versionDate' => '2020_11_11',
            'CBModel_versionDate_property' => '2022_01_15',
        ];

        if (
            $actualUpgradedSpec != $expectedUpgradedSpec
        ) {
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
    /* upgrade() */



    /**
     * @return object
     */
    static function
    CBTest_upgradeSelectedMainMenuItemName(
    ): stdClass {
        $tests = [

            (object)[
                'spec' => (object)[
                    'className' => 'CBViewPage',
                ],
                'expectedSelectedMenuItemNames' => '',
            ],

            (object)[
                'spec' => (object)[
                    'className' => 'CBViewPage',
                    'selectedMainMenuItemName' => 'blog',
                ],
                'expectedSelectedMenuItemNames' => 'blog',
            ],

            (object)[
                'spec' => (object)[
                    'className' => 'CBViewPage',
                    'selectedMainMenuItemName' => 'blog',
                    'selectedMenuItemNames' => 'main test'
                ],
                'expectedSelectedMenuItemNames' => 'main test',
            ],

        ];

        for (
            $index = 0;
            $index < count($tests);
            $index += 1
        ) {
            $test = $tests[$index];
            $originalSpec = $test->spec;

            $upgradedSpec = CBModel::upgrade(
                $originalSpec
            );

            $actualSelectedMenuItemNames = CBModel::valueToString(
                $upgradedSpec,
                'selectedMenuItemNames'
            );

            if (
                $actualSelectedMenuItemNames !==
                $test->expectedSelectedMenuItemNames
            ) {
                return CBTest::resultMismatchFailure(
                    "selectedMenuItemNames, test index {$index}",
                    $actualSelectedMenuItemNames,
                    $test->expectedSelectedMenuItemNames
                );
            }

            if (
                isset($upgradedSpec->selectedMainMenuItemName)
            ) {
                return CBTest::valueIssueFailure(
                    "selectedMainMenuItemName, test index {$index}",
                    $upgradedSpec,
                    <<<EOT

                        The "selectedMainMenuItemName" property should not be
                        set on an upgraded CBViewPage spec.

                    EOT,
                );
            }
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_upgradeSelectedMainMenuItemName() */



    /**
     * @return object
     */
    static function
    upgradeURI(
    ): stdClass {
        $testModelCBID =
        '5c165b79a9dbbd13966f01c0b63412c1cf96be9e';

        CBDB::transaction(
            function () use (
                $testModelCBID
            ) {
                CBModels::deleteByID(
                    $testModelCBID
                );
            }
        );

        // prepare
        {
            $originalSpec =
            CBModel::createSpec(
                'CBViewPage',
                $testModelCBID
            );

            CBViewPage::setPageSettingsClassName(
                $originalSpec,
                'CB_Placeholder'
            );
        }
        // prepare

        $originalSpec->URI =
        'a123456789a123456789a123456789a123456789a123456789' .
        'a123456789a123456789a123456789a123456789a123456789' .
        'a123456789a123456789a123456789a123456789a123456789';

        $upgradedSpec = CBModel::upgrade(
            $originalSpec
        );

        $expectedResult =
        'a123456789a123456789a123456789a123456789a123456789' .
        'a123456789a123456789a123456789a123456789a123456789';

        $actualResult =
        CBViewPage::getURI(
            $upgradedSpec
        );

        if (
            $actualResult !== $expectedResult
        ) {
            return CBTest::resultMismatchFailure(
                "upgrade URI",
                $actualResult,
                $expectedResult
            );
        }

        CBDB::transaction(
            function () use (
                $testModelCBID,
                $upgradedSpec
            ) {
                CBModels::save(
                    $upgradedSpec
                );

                CBModels::deleteByID(
                    $testModelCBID
                );
            }
        );

        return (object)[
            'succeeded' => true,
        ];
    }
    // upgradeURI()
}



/**
 *
 */
final class CBViewPageTests_PageFrame {

    static function CBViewPage_getFrameSearchText(
    ): string {
        return 'frame search text';
    }

}
