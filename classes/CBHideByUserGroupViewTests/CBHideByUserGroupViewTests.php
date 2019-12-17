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
            'groupName' => null,
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
            (object)[
                'name' => 'invalid group name',

                'originalSpec' => (object)[
                    'className' => 'CBHideByUserGroupView',
                    'groupName' => '  $  ',
                ],

                'expectedUpgradedSpec' => (object)[
                    'className' => 'CBHideByUserGroupView',
                    'subviews' => [],
                ],
            ],
            (object)[
                'name' => 'white space around group name',

                'originalSpec' => (object)[
                    'className' => 'CBHideByUserGroupView',
                    'groupName' => '  Administrators  ',
                ],

                'expectedUpgradedSpec' => (object)[
                    'className' => 'CBHideByUserGroupView',
                    'subviews' => [],
                    'userGroupClassName' => 'CBAdministratorsUserGroup',
                ],
            ],
            (object)[
                'name' => 'public',

                'originalSpec' => (object)[
                    'className' => 'CBHideByUserGroupView',
                    'groupName' => 'Public',
                ],

                'expectedUpgradedSpec' => (object)[
                    'className' => 'CBHideByUserGroupView',
                    'subviews' => [],
                    'userGroupClassName' => 'CBPublicUserGroup',
                ],
            ],
            (object)[
                'name' => 'simple upgrade administrators',

                'originalSpec' => (object)[
                    'className' => 'CBHideByUserGroupView',
                    'groupName' => 'Administrators',
                ],

                'expectedUpgradedSpec' => (object)[
                    'className' => 'CBHideByUserGroupView',
                    'subviews' => [],
                    'userGroupClassName' => 'CBAdministratorsUserGroup'
                ],
            ],
            (object)[
                'name' => 'simple upgrade developers',

                'originalSpec' => (object)[
                    'className' => 'CBHideByUserGroupView',
                    'groupName' => 'Developers',
                ],

                'expectedUpgradedSpec' => (object)[
                    'className' => 'CBHideByUserGroupView',
                    'subviews' => [],
                    'userGroupClassName' => 'CBDevelopersUserGroup'
                ],
            ],
            (object)[
                'name' => 'both properties set',

                'originalSpec' => (object)[
                    'className' => 'CBHideByUserGroupView',
                    'groupName' => 'Administrators',
                    'userGroupClassName' => 'CBDevelopersUserGroup',
                ],

                'expectedUpgradedSpec' => (object)[
                    'className' => 'CBHideByUserGroupView',
                    'subviews' => [],
                    'userGroupClassName' => 'CBDevelopersUserGroup',
                ],
            ],
            (object)[
                'name' => 'old property set to user group class name',

                'originalSpec' => (object)[
                    'className' => 'CBHideByUserGroupView',
                    'groupName' => 'CBDevelopersUserGroup',
                ],

                'expectedUpgradedSpec' => (object)[
                    'className' => 'CBHideByUserGroupView',
                    'subviews' => [],
                    'userGroupClassName' => 'CBDevelopersUserGroup'
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
