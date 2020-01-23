<?php

final class CBGeneralAdminMenu {

    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $adminMenuSpec = CBModels::fetchSpecByID(
            CBAdminMenu::getModelCBID()
        );

        $adminMenuSpec->items[] = (object)[
            'className' => 'CBMenuItem',
            'name' => 'general',
            'submenuID' => CBGeneralAdminMenu::getModelCBID(),
            'text' => 'General',
            'URL' => '/admin/',
        ];

        $generalAdminMenuSpec = (object)[
            'className' => 'CBMenu',
            'ID' => CBGeneralAdminMenu::getModelCBID(),
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
            function () use ($adminMenuSpec, $generalAdminMenuSpec) {
                CBModels::save($adminMenuSpec);

                CBModels::deleteByID(
                    CBGeneralAdminMenu::getModelCBID()
                );

                CBModels::save($generalAdminMenuSpec);
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
        ];
    }



    /* -- functions -- -- -- -- -- */



    /**
     * @return string
     */
    static function getModelCBID(): string {
        return '1668c3011f7be273731903b012a5884628300898';
    }



    /**
     * @deprecated use CBGeneralAdminMenu::getModelCBID()
     */
    static function getModelID(): string {
        return CBGeneralAdminMenu::getModelCBID();
    }

}
