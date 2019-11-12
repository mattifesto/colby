<?php

final class CBUsers_Tests {

    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'name' => 'installUserGroup_CBUsersTestUsers',
                'title' => (
                    'CBUsers: Install the "CBUsersTestUsers" user group.'
                ),
                'type' => 'interactive_server',
            ],
            (object)[
                'name' => 'uninstallUserGroup_CBUsersTestUsers',
                'title' => (
                    'CBUsers: Uninstall the "CBUsersTestUsers" user group.'
                ),
                'type' => 'interactive_server',
            ],
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
    static function CBTest_installUserGroup_CBUsersTestUsers(): stdClass {
        CBUsers::installUserGroup('CBUsersTestUsers');

        CBUserSettingsManagerCatalog::installUserSettingsManager(
            'CBUsersTestUsers_UserSettingsManager',
            0
        );

        return (object)[
            'succeeded' => 'true',
        ];
    }
    /* CBTest_installUserGroup_CBUsersTestUsers() */



    /**
     * @return object
     */
    static function CBTest_uninstallUserGroup_CBUsersTestUsers(): stdClass {
        CBUsers::uninstallUserGroup('CBUsersTestUsers');

        return (object)[
            'succeeded' => 'true',
        ];
    }
    /* CBTest_uninstallUserGroup_CBUsersTestUsers() */



    /**
     * @return object
     */
    static function CBTest_upgrade(): stdClass {
        $originalSpecs = [
            (object)[
                'className' => 'CBUser',
                'userID' => 100,
            ],
            (object)[
                'className' => 'CBUser',
                'userNumericID' => 101,
                'userID' => 99,
            ],
            (object)[
                'className' => 'CBUser',
                'userNumericID' => 102,
            ],
            (object)[
                'className' => 'CBUser',
                'userNumericID' => 103,
                'facebook' => (object)[
                    'name' => 'Bob',
                    'id' => '503',
                ],
            ],
            (object)[
                'className' => 'CBUser',
                'userNumericID' => 104,
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
                'userNumericID' => 100,
                'facebookUserID' => null,
                'facebookName' => '',
            ],
            (object)[
                'className' => 'CBUser',
                'userNumericID' => 101,
                'facebookUserID' => null,
                'facebookName' => '',
            ],
            (object)[
                'className' => 'CBUser',
                'userNumericID' => 102,
                'facebookUserID' => null,
                'facebookName' => '',
            ],
            (object)[
                'className' => 'CBUser',
                'userNumericID' => 103,
                'facebookUserID' => 503,
                'facebookName' => 'Bob',
            ],
            (object)[
                'className' => 'CBUser',
                'userNumericID' => 104,
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
