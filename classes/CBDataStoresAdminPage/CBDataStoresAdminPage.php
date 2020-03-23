<?php

final class CBDataStoresAdminPage {

    /* -- CBAdmin interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBAdmin_menuNamePath(): array {
        return [
            'develop',
            'datastores'
        ];
    }



    /**
     * @return string
     */
    static function CBAdmin_getUserGroupClassName(): string {
        return 'CBDevelopersUserGroup';
    }



    /**
     * @return void
     */
    static function CBAdmin_render(): void {
        CBHTMLOutput::pageInformation()->title = 'Data Stores Administration';
    }



    /* -- CBAjax interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function CBAjax_fetchData(): array {
        $SQL = <<<EOT

            SELECT      m.className as className,
                        LOWER(HEX(ds.ID)) as ID

            FROM        CBDataStores AS ds

            LEFT JOIN   CBModels AS m
                ON      ds.ID = m.ID

            ORDER BY    className,
                        ID

        EOT;

        return CBDB::SQLToObjects($SQL);
    }
    /* CBAjax_fetchData() */



    /**
     * @return string
     */
    static function CBAjax_fetchData_getUserGroupClassName(): string {
        return 'CBDevelopersUserGroup';
    }



    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v591.js', cbsysurl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBUI',
            'CBUIActionPart',
            'CBUINavigationArrowPart',
            'CBUINavigationView',
            'CBUIPanel',
            'CBUISectionItem4',
            'CBUISelector',
            'CBUITitleAndDescriptionPart',
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
                'ID' => CBDevelopAdminMenu::ID(),
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
                'name' => 'datastores',
                'text' => 'Data Stores',
                'URL' => CBAdmin::getAdminPageURL('CBDataStoresAdminPage'),
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
            'CBDevelopAdminMenu',
        ];
    }

}
