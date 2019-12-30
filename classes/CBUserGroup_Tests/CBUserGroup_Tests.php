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
