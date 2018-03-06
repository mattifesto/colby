<?php

final class CBAdminMenu {

    /**
     * This function resets the main admin menu during install. To add a menu
     * item create a class with an install dependency on this class and in the
     * CBInstall_install function add a menu item.
     *
     * @return void
     */
    static function CBInstall_install(): void {
        $spec = (object)[
            'className' => 'CBMenu',
            'ID' => CBAdminMenu::ID(),
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

        CBDB::transaction(function () use ($spec) {
            CBModels::deleteByID(CBAdminMenu::ID());
            CBModels::save($spec);
        });
    }

    /**
     * @return hex160
     */
    static function ID(): string {
        return '3924c0a0581171f86f0708bfa799a3d8c34bd390';
    }
}
