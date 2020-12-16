<?php

final class CBUsers_Tests {

    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'name' => 'upgrade',
                'title' => 'CBUser upgrade',
                'type' => 'server',
            ],
        ];
    }
    /* CBTest_getTests() */



    /* -- tests -- -- -- -- -- */



    /**
     * @return object
     */
    static function CBTest_upgrade(): stdClass {
        $originalSpecs = [
            (object)[
                'className' => 'CBUser',
            ],
            (object)[
                'className' => 'CBUser',
                'facebook' => (object)[
                    'name' => 'Bob',
                    'id' => '503',
                ],
            ],
            (object)[
                'className' => 'CBUser',
                'facebook' => (object)[
                    'name' => 'Bob',
                    'id' => '99',
                ],
                'facebookUserID' => 504,
                'facebookName' => 'Chet',
            ],
        ];

        $upgradedSpecs = [
            (object)[
                'className' => 'CBUser',
                'facebookUserID' => null,
                'facebookName' => '',
            ],
            (object)[
                'className' => 'CBUser',
                'facebookUserID' => 503,
                'facebookName' => 'Bob',
            ],
            (object)[
                'className' => 'CBUser',
                'facebookUserID' => 504,
                'facebookName' => 'Chet',
            ],
        ];

        for (
            $index = 0;
            $index < count($originalSpecs);
            $index += 1
        ) {
            $originalSpec = $originalSpecs[$index];

            $actualResult = CBModel::upgrade($originalSpec);

            $expectedResult = $upgradedSpecs[$index];

            if ($actualResult != $expectedResult) {
                return CBTest::resultMismatchFailure(
                    "test index {$index}",
                    $actualResult,
                    $expectedResult
                );
            }
        }


        return (object)[
            'succeeded' => 'true',
        ];
    }
    /* CBTest_upgrade() */

}
