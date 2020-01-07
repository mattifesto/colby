<?php

final class CBUser_Tests {

    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'name' => 'facebookUserIDToUserCBID',
                'type' => 'server',
            ],
            (object)[
                'name' => 'emailToUserCBID',
                'type' => 'server',
            ],

            (object)[
                'description' => <<<EOT

                    Resets the test user that has a Facebook account but no
                    email address or password to be used for testing.

                EOT,
                'name' => 'resetTestFacebookUser',
                'type' => 'interactive_server',
            ],
        ];
    }
    /* CBTest_getTests() */



    /* -- tests -- -- -- -- -- */



    /**
     * @return object
     */
    static function CBTest_emailToUserCBID(): stdClass {
        $testUserSpec = (object)[
            'className' => 'CBUser',
            'ID' => '77500994e2044c7adfd6f24daae36545baabc74d',
            'email' => 'bob_toodles_mcgarnagan@example.com',
        ];

        CBDB::transaction(
            function () use ($testUserSpec) {
                CBModels::deleteByID($testUserSpec->ID);
            }
        );


        /* test 1 */

        $actualResult = CBUser::emailToUserCBID(
            $testUserSpec->email
        );

        $expectedResult = null;

        if ($actualResult !== $expectedResult) {
            return CBTest::resultMismatchFailure(
                'test 1',
                $actualResult,
                $expectedResult
            );
        }


        /* test 2 */

        CBDB::transaction(
            function () use ($testUserSpec) {
                CBModels::save($testUserSpec);
            }
        );

        $actualResult = CBUser::emailToUserCBID(
            $testUserSpec->email
        );

        $expectedResult = $testUserSpec->ID;

        if ($actualResult !== $expectedResult) {
            return CBTest::resultMismatchFailure(
                'test 2',
                $actualResult,
                $expectedResult
            );
        }


        /* test 3 */

        CBDB::transaction(
            function () use ($testUserSpec) {
                CBModels::deleteByID($testUserSpec->ID);
            }
        );

        $actualResult = CBUser::emailToUserCBID(
            $testUserSpec->email
        );

        $expectedResult = null;

        if ($actualResult !== $expectedResult) {
            return CBTest::resultMismatchFailure(
                'test 3',
                $actualResult,
                $expectedResult
            );
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_emailToUserCBID() */



    /**
     * @return object
     */
    static function CBTest_facebookUserIDToUserCBID(): stdClass {
        $testUserSpec = (object)[
            'className' => 'CBUser',
            'ID' => '5a49b666af184ea80d39e0bd61cf0e1bad2f3a0a',
            'facebookUserID' => 1001,
            'title' => __METHOD__ . '()',
        ];

        CBDB::transaction(
            function () use ($testUserSpec) {
                CBModels::deleteByID($testUserSpec->ID);
            }
        );


        /* test 1 */

        $actualResult = CBUser::facebookUserIDToUserCBID(
            $testUserSpec->facebookUserID
        );

        $expectedResult = null;

        if ($actualResult !== $expectedResult) {
            return CBTest::resultMismatchFailure(
                'test 1',
                $actualResult,
                $expectedResult
            );
        }


        /* test 2 */

        CBDB::transaction(
            function () use ($testUserSpec) {
                CBModels::save($testUserSpec);
            }
        );

        $actualResult = CBUser::facebookUserIDToUserCBID(
            $testUserSpec->facebookUserID
        );

        $expectedResult = $testUserSpec->ID;

        if ($actualResult !== $expectedResult) {
            return CBTest::resultMismatchFailure(
                'test 2',
                $actualResult,
                $expectedResult
            );
        }


        /* test 3 */

        CBDB::transaction(
            function () use ($testUserSpec) {
                CBModels::deleteByID($testUserSpec->ID);
            }
        );

        $actualResult = CBUser::facebookUserIDToUserCBID(
            $testUserSpec->facebookUserID
        );

        $expectedResult = null;

        if ($actualResult !== $expectedResult) {
            return CBTest::resultMismatchFailure(
                'test 3',
                $actualResult,
                $expectedResult
            );
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_facebookUserIDToUserCBID() */



    /**
     * @return object
     */
    static function CBTest_resetTestFacebookUser(): stdClass {
        $userSpec = (object)[
            'className' => 'CBUser',
            'ID' => 'cd886ea6c33fd3bf5601c36b398a5ead535448eb',
            'facebookUserID' => 1,
            'title' => 'Test User via CBTest_resetTestFacebookUser()',
        ];

        CBDB::transaction(
            function () use ($userSpec) {
                CBModels::deleteByID($userSpec->ID);
            }
        );

        CBDB::transaction(
            function () use ($userSpec) {
                CBModels::save($userSpec);
            }
        );

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_resetTestFacebookUser() */

}
