<?php

final class CBUsersAdmin {

    /* -- CBAdmin interfaces -- -- -- -- -- */



    /**
     * @return string
     */
    static function CBAdmin_getUserGroupClassName(): string {
        return 'CBAdministratorsUserGroup';
    }



    /**
     * @return [string]
     */
    static function CBAdmin_menuNamePath(): array {
        return [
            'general',
            'users',
        ];
    }



    /**
     * @return void
     */
    static function CBAdmin_render(): void {
        CBHTMLOutput::pageInformation()->title = 'Users Administration';
    }



    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v565.js', cbsysurl()),
        ];
    }



    /**
     * @return [[<name>, <value>]]
     */
    static function CBHTMLOutput_JavaScriptVariables(): array {
        return [
            [
                'CBUsersAdmin_users',
                CBUsersAdmin::fetchUsers(),
            ],
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBUI',
            'CBUser',
            'Colby',
        ];
    }



    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $spec = CBModels::fetchSpecByID(
            CBGeneralAdminMenu::getModelID()
        );

        $spec->items[] = (object)[
            'className' => 'CBMenuItem',
            'name' => 'users',
            'text' => 'Users',
            'URL' => '/admin/?c=CBUsersAdmin',
        ];

        CBDB::transaction(
            function () use ($spec) {
                CBModels::save($spec);
            }
        );
    }



    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBGeneralAdminMenu',
        ];
    }



    /* -- functions -- -- -- -- -- */



    /**
     * @return [stdClass]
     */
    private static function fetchUsers() {
        $SQL = <<<EOT

            SELECT      LOWER(HEX(hash)) as hash,
                        email,
                        facebookName

            FROM        ColbyUsers

            ORDER BY    facebookName

        EOT;

        return CBDB::SQLToObjects($SQL);
    }
    /* fetchUsers() */

}
