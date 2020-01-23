<?php

final class CBAdminMenu {

    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * This function resets the main admin menu during install. To add a menu
     * item create a class with an install dependency on this class and in the
     * CBInstall_install function add a menu item.
     *
     * @return void
     */
    static function CBInstall_install(): void {
        $adminMenuSpec = (object)[
            'className' => 'CBMenu',
            'ID' => CBAdminMenu::getModelCBID(),
            'title' => 'Administration',
            'titleURI' => '/admin/',
            'items' => [
                (object)[
                    'className' => 'CBMenuItem',
                    'name' => 'home',
                    'text' => 'Home',
                    'URL' => '/',
                ],
            ],
        ];

        CBDB::transaction(
            function () use ($adminMenuSpec) {
                CBModels::deleteByID(
                    CBAdminMenu::getModelCBID()
                );

                CBModels::save($adminMenuSpec);
            }
        );
    }
    /* CBInstall_install() */



    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBModels'
        ];
    }



    /* -- functions -- -- -- -- -- */



    /**
     * @return CBID
     */
    static function getModelCBID(): string {
        return '3924c0a0581171f86f0708bfa799a3d8c34bd390';
    }



    /**
     * @deprecated use CBAdminMenu::getModelCBID()
     */
    static function ID(): string {
        return CBAdminMenu::getModelCBID();
    }

}
