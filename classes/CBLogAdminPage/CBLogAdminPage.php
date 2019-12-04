<?php

final class CBLogAdminPage {

    /* -- CBAdmin interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBAdmin_menuNamePath(): array {
        return [
            'general',
            'log',
        ];
    }



    /**
     * @return void
     */
    static function CBAdmin_render(): void {
        CBHTMLOutput::pageInformation()->title = 'Website Log';
    }



    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v529.js', cbsysurl()),
        ];
    }



    /**
     * @return [[<name>, <value>]]
     */
    static function CBHTMLOutput_JavaScriptVariables() {
        $SQL = <<<EOT

            SELECT DISTINCT sourceClassName
            FROM CBLog

        EOT;

        $classNames = array_values(
            array_filter(
                CBDB::SQLToArray($SQL)
            )
        );

        return [
            [
                'CBLogAdminPage_classNames',
                $classNames,
            ],
        ];
    }
    /* CBHTMLOutput_JavaScriptVariables() */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return [
            'CBErrorHandler',
            'CBModel',
            'CBUI',
            'CBUIExpander',
            'CBUINavigationView',
            'CBUISelector',
            'Colby',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $updater = CBModelUpdater::fetch(
            (object)[
                'ID' => CBGeneralAdminMenu::getModelID(),
            ]
        );

        $items = CBModel::valueToArray(
            $updater->working,
            'items'
        );

        array_push(
            $items,
            (object)[
                'className' => 'CBMenuItem',
                'name' => 'log',
                'text' => 'Log',
                'URL' => '/admin/?c=CBLogAdminPage',
            ]
        );

        $updater->working->items = $items;

        CBModelUpdater::save($updater);
    }
    /* CBInstall_install() */



    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBGeneralAdminMenu',
        ];
    }

}
