<?php

final class CBDevelopAdminMenu {

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $adminMenuSpec = CBModels::fetchSpecByID(CBAdminMenu::ID());

        $adminMenuSpec->items[] = (object)[
            'className' => 'CBMenuItem',
            'name' => 'develop',
            'submenuID' => CBDevelopAdminMenu::ID(),
            'text' => 'Develop',
            'URL' => CBAdmin::getAdminPageURL('CBAdminPageForUpdate'),
        ];

        $spec = (object)[
            'className' => 'CBMenu',
            'ID' => CBDevelopAdminMenu::ID(),
            'title' => 'Develop',
            'titleURI' => CBAdmin::getAdminPageURL('CBAdminPageForUpdate'),
            'items' => [
                (object)[
                    'className' => 'CBMenuItem',
                    'name' => 'datastores',
                    'text' => 'Data Stores',
                    'URL' => '/admin/page/?class=CBDataStoresAdminPage',
                ],
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


    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBAdminMenu',
            'CBHelpAdminMenu',
        ];
    }

    /**
     * @return hex160
     */
    static function ID(): string {
        return '8ba9210a82d3ab8f181dd7ac9619e06be65f930d';
    }
}
