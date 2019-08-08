<?php

final class CBGeneralAdminMenu {

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $adminMenuSpec = CBModels::fetchSpecByID(
            CBAdminMenu::ID()
        );

        $adminMenuSpec->items[] = (object)[
            'className' => 'CBMenuItem',
            'name' => 'general',
            'submenuID' => CBGeneralAdminMenu::getModelID(),
            'text' => 'General',
            'URL' => '/admin/',
        ];

        $spec = (object)[
            'className' => 'CBMenu',
            'ID' => CBGeneralAdminMenu::getModelID(),
            'title' => 'General',
            'titleURI' => '/admin/',
            'items' => [
                (object)[
                    'className' => 'CBMenuItem',
                    'name' => 'status',
                    'text' => 'Status',
                    'URL' => '/admin/',
                ],
            ],
        ];

        CBDB::transaction(
            function () use ($adminMenuSpec, $spec) {
                CBModels::save($adminMenuSpec);
                CBModels::deleteByID(CBGeneralAdminMenu::getModelID());
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
        ];
    }


    /**
     * @return string
     */
    static function getModelID(): string {
        return '1668c3011f7be273731903b012a5884628300898';
    }
}
