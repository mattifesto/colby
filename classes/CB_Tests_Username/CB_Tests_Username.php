<?php

final class
CB_Tests_Username {

    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function
    CBTest_getTests(
    ): array {
        return [
            (object)[
                'name' => 'process',
                'type' => 'server',
            ],
        ];
    }
    /* CBTest_getTests() */



    /* -- tests -- -- -- -- -- */



    /**
     * @return object
     */
    static function
    CBTest_process(
    ): stdClass {
        /* create specs */

        $userModelCBID = '7ed9fa03dbd585990639f2e09d77fac670978480';
        $prettyUsername1 = 'test_5eb0fa6266e2';
        $prettyUsername2 = 'test_ab19ddc54fdc';

        $userSpec = CBModel::createSpec(
            'CBUser',
            $userModelCBID
        );

        CBUser::setName(
            $userSpec,
            "Test User {$userModelCBID}"
        );

        CBUser::setEmailAddress(
            $userSpec,
            "{$userModelCBID}@{$userModelCBID}.com"
        );

        $usernameSpec1 = CB_Username::createSpec(
            $prettyUsername1,
            $userModelCBID
        );

        $usernameModelCBID1 = CBModel::getCBID(
            $usernameSpec1
        );

        $usernameSpec2 = CB_Username::createSpec(
            $prettyUsername2,
            $userModelCBID
        );

        $usernameModelCBID2 = CBModel::getCBID(
            $usernameSpec2
        );

        /* clean testing state */

        CBDB::transaction(
            function (
            ) use (
                $userModelCBID,
                $usernameModelCBID1,
                $usernameModelCBID2
            ) {
                $usernameModelCBIDs = [
                    $usernameModelCBID1,
                    $usernameModelCBID2,
                ];

                CBModels::deleteByID(
                    $userModelCBID,
                );

                CBModels::deleteByID(
                    $usernameModelCBIDs
                );
            }
        );

        /* save user model */

        CBDB::transaction(
            function () use ($userSpec) {
                CBModels::save(
                    $userSpec
                );
            }
        );

        /* save username model 1 */

        CBDB::transaction(
            function () use ($usernameSpec1) {
                CBModels::save(
                    $usernameSpec1
                );
            }
        );


        /* verify association */

        $actualUserModelCBID = CB_Username::fetchUserCBIDByUsernameCBID(
            $usernameModelCBID1
        );

        if (
            $actualUserModelCBID !== $userModelCBID
        ) {
            return CBTest::resultMismatchFailure(
                'username 1 confirmation',
                $actualUserModelCBID,
                $userModelCBID
            );
        }


        /* save username model 2 */

        CBDB::transaction(
            function () use ($usernameSpec2) {
                CBModels::save(
                    $usernameSpec2
                );
            }
        );


        /* verify association */

        $actualUserModelCBID = CB_Username::fetchUserCBIDByUsernameCBID(
            $usernameModelCBID2
        );

        if (
            $actualUserModelCBID !== $userModelCBID
        ) {
            return CBTest::resultMismatchFailure(
                'username 2 confirmation',
                $actualUserModelCBID,
                $userModelCBID
            );
        }


        /*
        CBDB::transaction(
            function () use ($userModelCBID) {
                CBModels::deleteByID(
                    $userModelCBID
                );
            }
        );
        */

        return (object)[
            'succeeded' => 'true',
        ];
    }
    /* CBTest_process() */

}
