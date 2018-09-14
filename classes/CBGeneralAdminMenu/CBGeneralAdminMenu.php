<?php

final class CBGeneralAdminMenu {

    const ID = '1668c3011f7be273731903b012a5884628300898';

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $adminMenuSpec = CBModels::fetchSpecByID(CBAdminMenu::ID());

        $adminMenuSpec->items[] = (object)[
            'className' => 'CBMenuItem',
            'name' => 'general',
            'submenuID' => CBGeneralAdminMenu::ID,
            'text' => 'General',
            'URL' => '/admin/',
        ];

        $spec = (object)[
            'className' => 'CBMenu',
            'ID' => CBGeneralAdminMenu::ID,
            'title' => 'General',
            'titleURI' => '/admin/',
            'items' => [
                (object)[
                    'className' => 'CBMenuItem',
                    'name' => 'status',
                    'text' => 'Status',
                    'URL' => '/admin/',
                ],
                (object)[
                    'className' => 'CBMenuItem',
                    'name' => 'log',
                    'text' => 'Log',
                    'URL' => '/admin/page/?class=CBLogAdminPage',
                ],
            ],
        ];

        CBDB::transaction(function () use ($adminMenuSpec, $spec) {
            CBModels::save($adminMenuSpec);
            CBModels::deleteByID(CBGeneralAdminMenu::ID);
            CBModels::save($spec);
        });
    }

    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBAdminMenu',
        ];
    }
}
