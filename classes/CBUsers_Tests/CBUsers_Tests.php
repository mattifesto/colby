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
                'userID' => 5,
            ],
            (object)[
                'className' => 'CBUser',
                'userNumericID' => 6,
                'userID' => 5,
            ],
            (object)[
                'className' => 'CBUser',
                'userNumericID' => 7,
            ],
        ];

        $upgradedSpecs = [
            (object)[
                'className' => 'CBUser',
                'userNumericID' => 5,
            ],
            (object)[
                'className' => 'CBUser',
                'userNumericID' => 6,
                'userID' => 5,
            ],
            (object)[
                'className' => 'CBUser',
                'userNumericID' => 7,
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
