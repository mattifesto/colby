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

}
