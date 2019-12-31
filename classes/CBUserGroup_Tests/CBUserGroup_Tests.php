<?php

final class CBUserGroup_Tests {

    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'name' => 'deprecatedGroupNameToUserGroupClassName',
                'type' => 'server',
            ],
            (object)[
                'name' => 'general',
                'type' => 'server',
            ],
            (object)[
                'name' => 'userIsMemberOfUserGroup',
                'type' => 'server',
            ],
        ];
    }
    /* CBTest_getTests() */



    /* -- tests -- -- -- -- -- */



    /**
     * @return object
     */
    static function CBTest_deprecatedGroupNameToUserGroupClassName(): stdClass {
        $tests = [
            [
                'Administrators',
                'CBAdministratorsUserGroup',
            ],
            [
                'Developers',
                'CBDevelopersUserGroup',
            ],
            [
                'Public',
                'CBPublicUserGroup',
            ],
            [
                '',
                null,
            ],
            [
                'random',
                null,
            ],
        ];

        for ($index = 0; $index < count($tests); $index += 1) {
            $test = $tests[$index];
            $testName = $test[0];
            $deprecatedGroupName = $test[0];
            $expectedUserGroupClassName = $test[1];

            $actualUserGroupClassName = (
                CBUserGroup::deprecatedGroupNameToUserGroupClassName(
                    $deprecatedGroupName
                )
            );

            if ($actualUserGroupClassName !== $expectedUserGroupClassName) {
                return CBTest::resultMismatchFailure(
                    $testName,
                    $actualUserGroupClassName,
                    $expectedUserGroupClassName
                );
            }
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_deprecatedGroupNameToUserGroupClassName() */



    /**
     * This test should perform complex situations such as added and removing
     * users and groups to test that convoluted scenarios produce expected
     * results.
     *
     * @NOTE 2019_12_31
     *
     *      This test was copied out of the ColbyUser_Tests class when an old
     *      group update function was removed. At that time the test was updated
     *      to work. It may need to be updated to consider primary testable
     *      situations.
     *
     * @return object
     */
    static function CBTest_general(): stdClass {
        $currentUserCBID = ColbyUser::getCurrentUserCBID();

        $userGroupClassName = 'CBTest_general_group';

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
            CBUserGroup::addUsers(
                $userGroupClassName,
                $currentUserCBID
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
            CBUserGroup::removeUsers(
                $userGroupClassName,
                $currentUserCBID
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
            CBUserGroup::addUsers(
                $userGroupClassName,
                $currentUserCBID
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
            CBUserGroup::addUsers(
                $userGroupClassName,
                $currentUserCBID
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
    static function CBTest_userIsMemberOfUserGroup(): stdClass {
        $currentUserCBID = ColbyUser::getCurrentUserCBID();
        $nonUserCBID = CBID::generateRandomCBID();

        $tests = [
            (object)[
                'userCBID' => $currentUserCBID,
                'groupName' => 'CBDevelopersUserGroup',
                'expectedResult' => true,
            ],
            (object)[
                'userCBID' => $currentUserCBID,
                'groupName' => 'CBAdministratorsUserGroup',
                'expectedResult' => true,
            ],
            (object)[
                'userCBID' => $currentUserCBID,
                'groupName' => 'CBPublicUserGroup',
                'expectedResult' => true,
            ],
            (object)[
                'userCBID' => $currentUserCBID,
                'groupName' => 'NO_EXIST',
                'expectedResult' => false,
            ],
            (object)[
                'userCBID' => $nonUserCBID,
                'groupName' => 'CBDevelopersUserGroup',
                'expectedResult' => false,
            ],
            (object)[
                'userCBID' => $nonUserCBID,
                'groupName' => 'CBAdministratorsUserGroup',
                'expectedResult' => false,
            ],
            (object)[
                'userCBID' => $nonUserCBID,
                'groupName' => 'CBPublicUserGroup',
                'expectedResult' => true,
            ],
            (object)[
                'userCBID' => $nonUserCBID,
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

            $actualResult = CBUserGroup::userIsMemberOfUserGroup(
                $test->userCBID,
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
    /* CBTest_userIsMemberOfUserGroup() */

}
