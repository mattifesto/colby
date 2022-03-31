<?php

final class
CBUser_Tests
{
    /* -- CBTest interfaces -- */



    /**
     * @return [object]
     */
    static function
    CBTest_getTests(
    ): array
    {
        return
        [
            // -- server



            (object)
            [
                'name' =>
                'facebookUserIDToUserCBID',

                'type' =>
                'server',
            ],
            (object)
            [
                'name' =>
                'emailToUserCBID',

                'type' =>
                'server',
            ],
            (object)
            [
                'name' =>
                'usernameChange',

                'type' =>
                'server'
            ],



            // -- interactive



            (object)[
                'description' =>
                <<<EOT

                    Resets the test user that has a Facebook account but no
                    email address or password to be used for testing.

                EOT,

                'name' =>
                'resetTestFacebookUser',

                'type' =>
                'interactive_server',
            ],

        ];
    }
    /* CBTest_getTests() */



    /* -- tests -- */



    /**
     * @return object
     */
    static function
    emailToUserCBID(
    ): stdClass
    {
        $testUserSpec =
        (object)
        [
            'className' =>
            'CBUser',

            'ID' =>
            '77500994e2044c7adfd6f24daae36545baabc74d',

            'email' =>
            'bob_toodles_mcgarnagan@example.com',
        ];

        CBDB::transaction(
            function () use (
                $testUserSpec
            ) {
                CBModels::deleteByID(
                    $testUserSpec->ID
                );
            }
        );


        /* test 1 */

        $actualResult =
        CBUser::emailToUserCBID(
            $testUserSpec->email
        );

        $expectedResult =
        null;

        if (
            $actualResult !== $expectedResult
        ) {
            return
            CBTest::resultMismatchFailure(
                'test 1',
                $actualResult,
                $expectedResult
            );
        }


        /* test 2 */

        CBDB::transaction(
            function () use (
                $testUserSpec
            ) {
                CBModels::save(
                    $testUserSpec
                );
            }
        );

        $actualResult =
        CBUser::emailToUserCBID(
            $testUserSpec->email
        );

        $expectedResult =
        $testUserSpec->ID;

        if (
            $actualResult !== $expectedResult
        ) {
            return
            CBTest::resultMismatchFailure(
                'test 2',
                $actualResult,
                $expectedResult
            );
        }


        /* test 3 */

        CBDB::transaction(
            function () use (
                $testUserSpec
            ) {
                CBModels::deleteByID(
                    $testUserSpec->ID
                );
            }
        );

        $actualResult =
        CBUser::emailToUserCBID(
            $testUserSpec->email
        );

        $expectedResult =
        null;

        if (
            $actualResult !== $expectedResult
        ) {
            return
            CBTest::resultMismatchFailure(
                'test 3',
                $actualResult,
                $expectedResult
            );
        }

        return
        (object)
        [
            'succeeded' => true,
        ];
    }
    /* CBTest_emailToUserCBID() */



    /**
     * @return object
     */
    static function
    facebookUserIDToUserCBID(
    ): stdClass
    {
        $testUserSpec =
        (object)
        [
            'className' =>
            'CBUser',

            'ID' =>
            '5a49b666af184ea80d39e0bd61cf0e1bad2f3a0a',

            'facebookUserID' =>
            1001,

            'title' =>
             __METHOD__ .
             '()',
        ];

        CBDB::transaction(
            function () use (
                $testUserSpec
            ) {
                CBModels::deleteByID(
                    $testUserSpec->ID
                );
            }
        );


        /* test 1 */

        $actualResult =
        CBUser::facebookUserIDToUserCBID(
            $testUserSpec->facebookUserID
        );

        $expectedResult =
        null;

        if (
            $actualResult !== $expectedResult
        ) {
            return
            CBTest::resultMismatchFailure(
                'test 1',
                $actualResult,
                $expectedResult
            );
        }


        /* test 2 */

        CBDB::transaction(
            function () use (
                $testUserSpec
            ) {
                CBModels::save(
                    $testUserSpec
                );
            }
        );

        $actualResult =
        CBUser::facebookUserIDToUserCBID(
            $testUserSpec->facebookUserID
        );

        $expectedResult =
        $testUserSpec->ID;

        if (
            $actualResult !== $expectedResult
        ) {
            return
            CBTest::resultMismatchFailure(
                'test 2',
                $actualResult,
                $expectedResult
            );
        }


        /* test 3 */

        CBDB::transaction(
            function () use (
                $testUserSpec
            ) {
                CBModels::deleteByID(
                    $testUserSpec->ID
                );
            }
        );

        $actualResult =
        CBUser::facebookUserIDToUserCBID(
            $testUserSpec->facebookUserID
        );

        $expectedResult =
        null;

        if (
            $actualResult !== $expectedResult
        ) {
            return
            CBTest::resultMismatchFailure(
                'test 3',
                $actualResult,
                $expectedResult
            );
        }

        return
        (object)
        [
            'succeeded' => true,
        ];
    }
    /* CBTest_facebookUserIDToUserCBID() */



    /**
     * @return object
     */
    static function
    resetTestFacebookUser(
    ): stdClass
    {
        $userSpec =
        (object)
        [
            'className' =>
            'CBUser',

            'ID' =>
            'cd886ea6c33fd3bf5601c36b398a5ead535448eb',

            'facebookUserID' =>
            1,

            'title' =>
            'Test User via CBTest_resetTestFacebookUser()',
        ];

        CBDB::transaction(
            function () use (
                $userSpec
            ) {
                CBModels::deleteByID(
                    $userSpec->ID
                );
            }
        );

        CBDB::transaction(
            function () use (
                $userSpec
            ) {
                CBModels::save(
                    $userSpec
                );
            }
        );

        return
        (object)
        [
            'succeeded' =>
            true,
        ];
    }
    /* CBTest_resetTestFacebookUser() */



    /**
     * @NOTE 2022_03_30
     *
     *      This test exists because of a bug where usernames were not being
     *      unregistered when the switched to a new username.
     */
    static function
    usernameChange(
    ): stdClass
    {
        $testUserModelCBID =
        '6f1bc18361f1e3a30a4fcb41ea1115684d1c4e4c';

        $testUserPrettyUsername1 =
        CBUser::generateRandomAvailablePrettyUsername();

        $testUserPrettyUsername2 =
        CBUser::generateRandomAvailablePrettyUsername();



        // -- prepare

        CBDB::transaction(
            function () use (
                $testUserModelCBID
            ) {
                CBModels::deleteByID(
                    $testUserModelCBID
                );
            }
        );

        $testUserSpec =
        CBModel::createSpec(
            'CBUser',
            $testUserModelCBID
        );

        CBUser::setEmailAddress(
            $testUserSpec,
            "${testUserModelCBID}@example.com"
        );

        CBUser::setPrettyUsername(
            $testUserSpec,
            $testUserPrettyUsername1
        );

        CBDB::transaction(
            function () use (
                $testUserSpec
            ) {
                CBModels::save(
                    $testUserSpec
                );
            }
        );



        // -- username 1

        $expectedResult =
        CB_Username::prettyUsernameToUsernameModelCBID(
            $testUserPrettyUsername1
        );

        $association =
        CBModelAssociations::fetchOne(
            $testUserModelCBID,
            'CBUser_username_association'
        );

        $actualResult =
        $association->associatedID;

        if (
            $actualResult !== $expectedResult
        ) {
            return
            CBTest::resultMismatchFailure(
                'username 1',
                $actualResult,
                $expectedResult
            );
        }



        // -- prepare

        $newVersion =
        CBModel::getVersion(
            $testUserSpec
        ) +
        1;

        CBModel::setVersion(
            $testUserSpec,
            $newVersion
        );

        CBUser::setPrettyUsername(
            $testUserSpec,
            $testUserPrettyUsername2
        );

        CBDB::transaction(
            function () use (
                $testUserSpec
            ) {
                CBModels::save(
                    $testUserSpec
                );
            }
        );



        // -- username 2

        $expectedResult =
        CB_Username::prettyUsernameToUsernameModelCBID(
            $testUserPrettyUsername2
        );

        /**
         * With the original bug, this call to fetchOne() would fail because
         * there was more than one username associated with the user.
         */
         
        $association =
        CBModelAssociations::fetchOne(
            $testUserModelCBID,
            'CBUser_username_association'
        );

        $actualResult =
        $association->associatedID;

        if (
            $actualResult !== $expectedResult
        ) {
            return
            CBTest::resultMismatchFailure(
                'username 2',
                $actualResult,
                $expectedResult
            );
        }



        // -- clean up

        CBDB::transaction(
            function () use (
                $testUserModelCBID
            ) {
                CBModels::deleteByID(
                    $testUserModelCBID
                );
            }
        );



        // -- done

        return
        (object)
        [
            'succeeded' =>
            true,
        ];
    }
    // usernameChange()

}
