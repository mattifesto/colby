<?php

final class ColbyUser_Tests {

    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'name' => 'createNewTestUser',
                'type' => 'interactive_server',
            ],
            (object)[
                'name' => 'general',
                'title' => 'ColbyUser',
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
     * This test creates a new user to be used for testing.
     *
     * For now, this user has a random Facebook ID since that is currently
     * required and is not great, but let's face it, there aren't likely to be
     * conflicts.
     *
     * Eventually users will be easy to create with just an email address and
     * this test can be removed.
     *
     * @return object
     */
    static function CBTest_createNewTestUser(): stdClass {
        $facebookUserID = 29384398;
        $facebookAccessToken = 'invalid_test';
        $facebookName = 'Clay Cartwright (Test User)';

        ColbyUser::updateFacebookUser(
            $facebookUserID,
            $facebookAccessToken,
            $facebookName
        );

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_createNewTestUser() */



    /**
     * This test should perform complex situations such as added and removing
     * users and groups to test that convoluted scenarios produce expected
     * results.
     *
     * @return object
     */
    static function CBTest_general(): stdClass {
        $currentUserCBID = ColbyUser::getCurrentUserCBID();

        $currentUserNumericID = CBUsers::forTesting_userCBIDtoUserNumericID(
            $currentUserCBID
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

            $actualResult = CBUserGroup::userIsMemberOfUserGroup(
                $currentUserCBID,
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

            $actualResult = CBUserGroup::userIsMemberOfUserGroup(
                $currentUserCBID,
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

            $actualResult = CBUserGroup::userIsMemberOfUserGroup(
                $currentUserCBID,
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

            $actualResult = CBUserGroup::userIsMemberOfUserGroup(
                $currentUserCBID,
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

            $actualResult = CBUserGroup::userIsMemberOfUserGroup(
                $currentUserCBID,
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
