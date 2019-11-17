<?php

final class ColbyUser_Tests {

    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
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



    /* -- tests -- -- -- -- -- */



    /**
     * @return object
     */
    static function CBTest_isMemberOfGroup(): stdClass {
        $tests = [
            (object)[
                'userNumericID' => ColbyUser::currentUserID(),
                'groupName' => 'Developers',
                'expectedResult' => true,
            ],
            (object)[
                'userNumericID' => ColbyUser::currentUserID(),
                'groupName' => 'Administrators',
                'expectedResult' => true,
            ],
            (object)[
                'userNumericID' => ColbyUser::currentUserID(),
                'groupName' => 'Public',
                'expectedResult' => true,
            ],
            (object)[
                'userNumericID' => ColbyUser::currentUserID(),
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
