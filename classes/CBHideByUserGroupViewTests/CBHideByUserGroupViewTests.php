<?php

final class CBHideByUserGroupViewTests {

    /* -- CBTest interfaces -- -- -- -- -- */



    static function CBTest_getTests(): array {
        return [
            (object)[
                'name' => 'general',
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
        $spec = (object)[
            'className' => 'CBHideByUserGroupView',
            'subviews' => CBViewTests::testSubviewSpecs(),
        ];


        /* build */

        $expectedModel = (object)[
            'className' => 'CBHideByUserGroupView',
            'hideFromMembers' => false,
            'hideFromNonmembers' => false,
            'subviews' => CBViewTests::testSubviewModels(),
            'userGroupClassName' => null,
        ];

        $actualModel = CBModel::build($spec);

        if ($actualModel != $expectedModel) {
            return CBTest::resultMismatchFailure(
                'build',
                $actualModel,
                $expectedModel
            );
        }


        /* toSearchText */

        $actualSearchText = CBModel::toSearchText($actualModel);

        $expectedSearchText = (
            CBViewTests::testSubviewSearchText() .
            ' CBHideByUserGroupView'
        );

        if ($actualSearchText !== $expectedSearchText) {
            return CBTest::resultMismatchFailure(
                'toSearchText',
                $actualSearchText,
                $expectedSearchText
            );
        }


        /* upgrade */

        $actualUpgradedSpec = CBModel::upgrade($spec);

        $expectedUpgradedSpec = (object)[
            'className' => 'CBHideByUserGroupView',
            'subviews' => CBViewTests::testSubviewUpgradedSpecs(),
        ];

        if ($actualUpgradedSpec != $expectedUpgradedSpec) {
            return CBTest::resultMismatchFailure(
                'upgrade',
                $actualUpgradedSpec,
                $expectedUpgradedSpec
            );
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTests_general() */



    /**
     * @return object
     */
    static function CBTest_upgrade(): stdClass {
        $tests = [
            (object)[
                'name' => 'empty',

                'originalSpec' => (object)[
                    'className' => 'CBHideByUserGroupView',
                ],

                'expectedUpgradedSpec' => (object)[
                    'className' => 'CBHideByUserGroupView',
                    'subviews' => [],
                ],
            ],
        ];

        for ($index = 0; $index < count($tests); $index +=1) {
            $test = $tests[$index];

            $actualUpgradeSpec = CBModel::upgrade(
                $test->originalSpec
            );

            if ($actualUpgradeSpec != $test->expectedUpgradedSpec) {
                return CBTest::resultMismatchFailure(
                    $test->name,
                    $actualUpgradeSpec,
                    $test->expectedUpgradedSpec
                );
            }
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_upgrade() */

}
