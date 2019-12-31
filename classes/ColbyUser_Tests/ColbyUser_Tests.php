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
