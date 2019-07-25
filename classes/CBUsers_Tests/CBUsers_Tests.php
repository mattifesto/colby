<?php

final class CBUsers_Tests {

    /* -- CBTest interfaces -- -- -- -- -- */

    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'type' => 'interactive_server',
                'title' => (
                    'CBUsers: Install the "CBUsersTestUsers" user group.'
                ),
                'name' => 'installUserGroup_CBUsersTestUsers',
            ],
            (object)[
                'type' => 'interactive_server',
                'title' => (
                    'CBUsers: Uninstall the "CBUsersTestUsers" user group.'
                ),
                'name' => 'uninstallUserGroup_CBUsersTestUsers',
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
}
