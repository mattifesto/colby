<?php

final class CBTest_SCPreferences {

    /* -- CBTest interfaces -- */



    /**
     * @return [object]
     */
    static function
    CBTest_getTests(
    ): array {
        return [
            (object)[
                'name' => 'orderNotificationsEmailAddresses',
                'type' => 'server',
            ],
        ];
    }
    /* CBTest_getTests() */



    /* -- tests -- */



    static function
    CBTest_orderNotificationsEmailAddresses(
    ): stdClass {
        $testCases = [

            (object)[
                'spec' => (object)[
                    'className' => 'SCPreferences',
                ],
                'expectedOrderNotificationsEmailAddresses' => [],
            ],

            (object)[
                'spec' => (object)[
                    'className' => 'SCPreferences',
                    'orderNotificationsEmailAddressesCSV' => (
                        'foo@foo.com, bar@bar.com, fred, "foo""@foo"".com"'
                    ),
                ],
                'expectedOrderNotificationsEmailAddresses' => [
                    'foo@foo.com',
                    'bar@bar.com',
                    'foo"@foo".com',
                ],
            ],

        ];

        for (
            $index = 0;
            $index < count($testCases);
            $index += 1
        ) {
            $testCase = $testCases[$index];

            $actualModel = CBModel::build(
                $testCase->spec
            );

            $actualOrderNotificationsEmailAddresses = (
                SCPreferences::getOrderNotificationsEmailAddresses(
                    $actualModel
                )
            );

            if (
                $actualOrderNotificationsEmailAddresses !=
                $testCase->expectedOrderNotificationsEmailAddresses
            ) {
                return CBTest::resultMismatchFailure(
                    "test index {$index}",
                    $actualOrderNotificationsEmailAddresses,
                    $testCase->expectedOrderNotificationsEmailAddresses
                );
            }
        }

        return  (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_build() */

}
