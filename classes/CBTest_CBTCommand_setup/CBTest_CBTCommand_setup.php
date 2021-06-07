<?php

final class
CBTest_CBTCommand_setup {

    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function
    CBTest_getTests(
    ): array {
        return [
            (object)[
                'name' => 'isNamespace',
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
    CBTest_isNamespace(
    ): stdClass {
        $tests = [
            [
                '',
                false,
            ],
            [
                '1',
                false,
            ],
            [
                '1A',
                false,
            ],
            [
                'a',
                false,
            ],
            [
                '.dog',
                false,
            ],
            [
                'A',
                true,
            ],
            [
                'AB',
                true,
            ],
            [
                'A1',
                true,
            ],
            [
                'A1B1Z1D2E9F0',
                true,
            ],
        ];

        foreach (
            $tests as $test
        ) {
            $value = $test[0];

            $actualResult = CBTCommand_setup::isNamespace(
                $value
            );

            $expectedResult = $test[1];

            if ($actualResult !== $expectedResult) {
                return CBTest::resultMismatchFailure(
                    json_encode($value),
                    $actualResult,
                    $expectedResult
                );
            }
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_isNamespace() */

}
