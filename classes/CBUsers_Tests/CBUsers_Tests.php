<?php

final class
CBUsers_Tests
{
    /* -- CBTest interfaces -- */



    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'name' => 'upgrade',
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
    upgrade(
    ): stdClass
    {
        $originalSpecs =
        [
            (object)
            [
                'className' =>
                'CBUser',

                'CBUser_prettyUsername_property' =>
                'BobBobberts',
            ],



            (object)[
                'className' =>
                'CBUser',

                'facebook' =>
                (object)
                [
                    'name' =>
                    'Bob',

                    'id' =>
                    '503',
                ],

                'CBUser_prettyUsername_property' =>
                'BobBobberts',
            ],



            (object)
            [
                'className' =>
                'CBUser',

                'facebook' =>
                (object)
                [
                    'name' =>
                    'Bob',

                    'id' =>
                    '99',
                ],

                'facebookUserID' =>
                504,

                'facebookName' =>
                'Chet',

                'CBUser_prettyUsername_property' =>
                'BobBobberts',
            ],
        ];

        $upgradedSpecs =
        [
            (object)
            [
                'className' =>
                'CBUser',

                'facebookUserID' =>
                null,

                'facebookName' =>
                '',

                'CBUser_prettyUsername_property' =>
                'BobBobberts',

                'CBModel_versionDate_property' =>
                '2022_01_15',

                'CBModel_processVersionNumber_property' =>
                '2022_07_24_1658674583',
            ],
            (object)
            [
                'className' =>
                'CBUser',

                'facebookUserID' =>
                503,

                'facebookName' =>
                'Bob',

                'CBUser_prettyUsername_property' =>
                'BobBobberts',

                'CBModel_versionDate_property' =>
                '2022_01_15',

                'CBModel_processVersionNumber_property' =>
                '2022_07_24_1658674583',
            ],
            (object)
            [
                'className' =>
                'CBUser',

                'facebookUserID' =>
                504,

                'facebookName' =>
                'Chet',

                'CBUser_prettyUsername_property' =>
                'BobBobberts',

                'CBModel_versionDate_property' =>
                '2022_01_15',

                'CBModel_processVersionNumber_property' =>
                '2022_07_24_1658674583',
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



        $result =
        (object)
        [
            'succeeded' =>
            'true',
        ];

        return $result;
    }
    /* CBTest_upgrade() */

}
