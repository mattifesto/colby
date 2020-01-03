<?php

final class CBUser_Tests {

    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'name' => 'emailToUserCBID',
                'type' => 'server',
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

        CBModels::deleteByID($testUserSpec->ID);


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

        CBModels::save($testUserSpec);

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

        CBModels::deleteByID($testUserSpec->ID);

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

}
