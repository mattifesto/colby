<?php

final class ColbyUser_Tests {

    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'name' => 'general',
                'title' => 'ColbyUser',
                'type' => 'server',
            ],
            (object)[
                'name' => 'isMemberOfGroup',
                'title' => 'ColbyUser::isMemberOfGroup()',
                'type' => 'server',
            ],
            (object)[
                'name' => 'updateFacebookUser',
                'title' => 'ColbyUser::updateFacebookUser()',
                'type' => 'server',
            ],
        ];
    }
    /* CBTest_getTests() */



    /* -- tests -- -- -- -- -- */



    /**
     * This test should perform complex situations such as added and removing
     * users and groups to test that convoluted scenarios produce expected
     * results.
     *
     * @return object
     */
    static function CBTest_general(): stdClass {
        $currentUserNumericID = CBUsers::forTesting_userCBIDtoUserNumericID(
            ColbyUser::getCurrentUserCBID()
        );

        $userGroupClassName = 'CBTest_updateGroupMembership_group';

        $userGroupSpec = (object)[
            'className' => 'CBUserGroup',

            'ID' => CBUserGroup::userGroupClassNameToCBID(
                $userGroupClassName
            ),

            'userGroupClassName' => $userGroupClassName,
        ];


        $testName = CBConvert::stringToCleanLine(<<<EOT
            Delete the test group model and make sure the current user is not a
            member of the test group.
        EOT);

        {
            CBModels::deleteByID($userGroupSpec->ID);

            $actualResult = ColbyUser::currentUserIsMemberOfGroup(
                $userGroupClassName
            );

            $expectedResult = false;

            if ($actualResult !== $expectedResult) {
                return CBTest::resultMismatchFailure(
                    $testName,
                    $actualResult,
                    $expectedResult
                );
            }
        }


        $testName = CBConvert::stringToCleanLine(<<<EOT
            Make sure the current user is not a member of the test group
            according to CBUserGroup::currentUserIsMemberOfUserGroup().
        EOT);

        {
            $actualResult = CBUserGroup::currentUserIsMemberOfUserGroup(
                $userGroupClassName
            );

            $expectedResult = false;

            if ($actualResult !== $expectedResult) {
                return CBTest::resultMismatchFailure(
                    $testName,
                    $actualResult,
                    $expectedResult
                );
            }
        }


        $testName = CBConvert::stringToCleanLine(<<<EOT
            Fetch all group models and make sure the test group model is not
            included.
        EOT);

        {
            $userGroupModels = CBUserGroup::fetchAllUserGroupModels();

            $actualResult = cb_array_any(
                function ($userGroupModel) use ($userGroupClassName) {
                    return (
                        $userGroupModel->userGroupClassName ===
                        $userGroupClassName
                    );
                },
                $userGroupModels
            );

            $expectedResult = false;

            if ($actualResult !== $expectedResult) {
                return CBTest::resultMismatchFailure(
                    $testName,
                    $actualResult,
                    $expectedResult
                );
            }
        }


        $testName = CBConvert::stringToCleanLine(<<<EOT
            Save the test group model, fetch all user group models, and make
            sure the test group model is included.
        EOT);

        {
            CBModels::save($userGroupSpec);

            $userGroupModels = CBUserGroup::fetchAllUserGroupModels();

            $actualResult = cb_array_any(
                function ($userGroupModel) use ($userGroupClassName) {
                    return (
                        $userGroupModel->userGroupClassName ===
                        $userGroupClassName
                    );
                },
                $userGroupModels
            );

            $expectedResult = true;

            if ($actualResult !== $expectedResult) {
                return CBTest::resultMismatchFailure(
                    $testName,
                    $actualResult,
                    $expectedResult
                );
            }
        }


        $testName = CBConvert::stringToCleanLine(<<<EOT
            Add the current user to the test group and then make sure the
            current user is a member of the test group.
        EOT);

        {
            ColbyUser::updateGroupMembership(
                $currentUserNumericID,
                $userGroupClassName,
                true
            );

            $actualResult = ColbyUser::currentUserIsMemberOfGroup(
                $userGroupClassName
            );

            $expectedResult = true;

            if ($actualResult !== $expectedResult) {
                return CBTest::resultMismatchFailure(
                    $testName,
                    $actualResult,
                    $expectedResult
                );
            }
        }


        $testName = CBConvert::stringToCleanLine(<<<EOT
            After adding the current user to the test group make sure the
            current user is a member of the test group according to
            CBUserGroup::currentUserIsMemberOfUserGroup().
        EOT);

        {
            $actualResult = CBUserGroup::currentUserIsMemberOfUserGroup(
                $userGroupClassName
            );

            $expectedResult = true;

            if ($actualResult !== $expectedResult) {
                return CBTest::resultMismatchFailure(
                    $testName,
                    $actualResult,
                    $expectedResult
                );
            }
        }


        $testName = CBConvert::stringToCleanLine(<<<EOT
            Remove the current user from the test group and then make sure the
            current user is not a member of the test group.
        EOT);

        {
            ColbyUser::updateGroupMembership(
                $currentUserNumericID,
                $userGroupClassName,
                false
            );

            $actualResult = ColbyUser::currentUserIsMemberOfGroup(
                $userGroupClassName
            );

            $expectedResult = false;

            if ($actualResult !== $expectedResult) {
                return CBTest::resultMismatchFailure(
                    $testName,
                    $actualResult,
                    $expectedResult
                );
            }
        }


        $testName = CBConvert::stringToCleanLine(<<<EOT
            After removing the current user from the test group and make sure
            the current user is not a member of the test group according to
            CBUserGroup::currentUserIsMemberOfUserGroup().
        EOT);

        {
            $actualResult = CBUserGroup::currentUserIsMemberOfUserGroup(
                $userGroupClassName
            );

            $expectedResult = false;

            if ($actualResult !== $expectedResult) {
                return CBTest::resultMismatchFailure(
                    $testName,
                    $actualResult,
                    $expectedResult
                );
            }
        }


        $testName = CBConvert::stringToCleanLine(<<<EOT
            Add the current user to the test group after having been removed and
            then make sure the current user is a member of the test group.
        EOT);

        {
            ColbyUser::updateGroupMembership(
                $currentUserNumericID,
                $userGroupClassName,
                true
            );

            $actualResult = ColbyUser::currentUserIsMemberOfGroup(
                $userGroupClassName
            );

            $expectedResult = true;

            if ($actualResult !== $expectedResult) {
                return CBTest::resultMismatchFailure(
                    $testName,
                    $actualResult,
                    $expectedResult
                );
            }
        }


        $testName = CBConvert::stringToCleanLine(<<<EOT
            After adding the current user to the test group after having been
            removed make sure the current user is a member of the test group
            according to CBUserGroup::currentUserIsMemberOfUserGroup().
        EOT);

        {
            ColbyUser::updateGroupMembership(
                $currentUserNumericID,
                $userGroupClassName,
                true
            );

            $actualResult = CBUserGroup::currentUserIsMemberOfUserGroup(
                $userGroupClassName
            );

            $expectedResult = true;

            if ($actualResult !== $expectedResult) {
                return CBTest::resultMismatchFailure(
                    $testName,
                    $actualResult,
                    $expectedResult
                );
            }
        }


        $testName = CBConvert::stringToCleanLine(<<<EOT
            Delete the test group and make sure the current user is no longer a
            member of the test group.
        EOT);

        {
            CBModels::deleteByID($userGroupSpec->ID);

            $actualResult = ColbyUser::currentUserIsMemberOfGroup(
                $userGroupClassName
            );

            $expectedResult = false;

            if ($actualResult !== $expectedResult) {
                return CBTest::resultMismatchFailure(
                    $testName,
                    $actualResult,
                    $expectedResult
                );
            }
        }


        $testName = CBConvert::stringToCleanLine(<<<EOT
            After deleting the test group make sure the current user is no
            longer a member of the test group according to
            CBUserGroup::currentUserIsMemberOfUserGroup().
        EOT);

        {
            CBModels::deleteByID($userGroupSpec->ID);

            $actualResult = CBUserGroup::currentUserIsMemberOfUserGroup(
                $userGroupClassName
            );

            $expectedResult = false;

            if ($actualResult !== $expectedResult) {
                return CBTest::resultMismatchFailure(
                    $testName,
                    $actualResult,
                    $expectedResult
                );
            }
        }


        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_general() */



    /**
     * @return object
     */
    static function CBTest_isMemberOfGroup(): stdClass {
        $currentUserNumericID = CBUsers::forTesting_userCBIDtoUserNumericID(
            ColbyUser::getCurrentUserCBID()
        );

        $tests = [
            (object)[
                'userNumericID' => $currentUserNumericID,
                'groupName' => 'Developers',
                'expectedResult' => true,
            ],
            (object)[
                'userNumericID' => $currentUserNumericID,
                'groupName' => 'Administrators',
                'expectedResult' => true,
            ],
            (object)[
                'userNumericID' => $currentUserNumericID,
                'groupName' => 'Public',
                'expectedResult' => true,
            ],
            (object)[
                'userNumericID' => $currentUserNumericID,
                'groupName' => 'NO_EXIST',
                'expectedResult' => false,
            ],
            (object)[
                'userNumericID' => PHP_INT_MAX,
                'groupName' => 'Developers',
                'expectedResult' => false,
            ],
            (object)[
                'userNumericID' => PHP_INT_MAX,
                'groupName' => 'Administrators',
                'expectedResult' => false,
            ],
            (object)[
                'userNumericID' => PHP_INT_MAX,
                'groupName' => 'Public',
                'expectedResult' => true,
            ],
            (object)[
                'userNumericID' => PHP_INT_MAX,
                'groupName' => 'NO_EXIST',
                'expectedResult' => false,
            ],
        ];

        for (
            $index = 0;
            $index < count($tests);
            $index += 1
        ) {
            $test = $tests[$index];

            $actualResult = ColbyUser::isMemberOfGroup(
                $test->userNumericID,
                $test->groupName
            );

            $expectedResult = $test->expectedResult;

            if ($actualResult !== $expectedResult) {
                return CBTest::resultMismatchFailure(
                    "test index {$index}",
                    $actualResult,
                    $expectedResult
                );
            }
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_isMemberOfGroup() */



    /**
     * @return object
     */
    static function CBTest_updateFacebookUser(): stdClass {
        $facebookUserID = 3000000000; // this is an invalid Facebook user ID
        $facebookAccessToken = 'fake';
        $facebookName = 'Test User for CBTest_updateFacebookUser()';

        $userSpec = ColbyUser::updateFacebookUser(
            $facebookUserID,
            $facebookAccessToken,
            $facebookName
        );

        $userCBID = $userSpec->ID;
        $userCBIDAsSQL = CBID::toSQL($userCBID);


        /* ColbyUsers row count 1 */

        $SQL = <<<EOT

            SELECT      COUNT(*)
            FROM        ColbyUsers
            WHERE       hash = {$userCBIDAsSQL}

        EOT;

        $actualResult = CBDB::SQLToValue($SQL);
        $expectedResult = '1';

        if ($actualResult !== $expectedResult) {
            return CBTest::resultMismatchFailure(
                'ColbyUsers row count 1',
                $actualResult,
                $expectedResult
            );
        }


        /* delete user */

        CBModels::deleteByID($userSpec->ID);


        /* ColbyUsers row count 2 */

        $SQL = <<<EOT

            SELECT      COUNT(*)
            FROM        ColbyUsers
            WHERE       hash = {$userCBIDAsSQL}

        EOT;

        $actualResult = CBDB::SQLToValue($SQL);
        $expectedResult = '0';

        if ($actualResult !== $expectedResult) {
            return CBTest::resultMismatchFailure(
                'ColbyUsers row count 2',
                $actualResult,
                $expectedResult
            );
        }


        /* done */

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_updateFacebookUser() */

}
