<?php

final class CBDevelopAdminMenu {

    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $adminMenuSpec = CBModels::fetchSpecByID(
            CBAdminMenu::ID()
        );

        $adminMenuSpec->items[] = (object)[
            'className' => 'CBMenuItem',

            'name' => 'develop',

            'submenuID' => CBDevelopAdminMenu::ID(),

            'text' => 'Develop',

            'URL' => CBAdmin::getAdminPageURL(
                'CBGitHistoryAdmin'
            ),
        ];

        $spec = (object)[
            'className' => 'CBMenu',

            'ID' => CBDevelopAdminMenu::ID(),

            'title' => 'Develop',

            'titleURI' => CBAdmin::getAdminPageURL(
                'CBGitHistoryAdmin'
            ),

            'items' => [
                (object)[
                    'className' => 'CBMenuItem',
                    'name' => 'php',
                    'text' => 'PHP',
                    'URL' => '/admin/?c=CBPHPAdmin',
                ],
                (object)[
                    'className' => 'CBMenuItem',
                    'name' => 'test',
                    'text' => 'Test',
                    'URL' => '/admin/?c=CBTestAdmin',
                ],
            ],
        ];

        CBDB::transaction(
            function () use ($adminMenuSpec, $spec) {
                CBModels::save($adminMenuSpec);
                CBModels::deleteByID(CBDevelopAdminMenu::ID());
                CBModels::save($spec);
            }
        );
    }
    /* CBInstall_install() */



    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBAdminMenu',
            'CBHelpAdminMenu',
        ];
    }



    /* -- functions -- -- -- -- -- */



    /**
     * @return CBID
     */
    static function ID(): string {
        return '8ba9210a82d3ab8f181dd7ac9619e06be65f930d';
    }

}
