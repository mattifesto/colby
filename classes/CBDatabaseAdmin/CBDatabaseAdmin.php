<?php

final class CBDatabaseAdmin {

    /**
     * @return string
     */
    static function CBAdmin_getUserGroupClassName(): string {
        return 'CBDevelopersUserGroup';
    }

    /**
     * @return [string]
     */
    static function CBAdmin_menuNamePath(): array {
        return ['develop', 'database'];
    }

    /**
     * @return void
     */
    static function CBAdmin_render(): void {
        CBHTMLOutput::pageInformation()->title = 'Database Administration';
    }

    /**
     * @return [[name, value]]
     */
    static function CBHTMLOutput_JavaScriptVariables(): array {
        return [
            ['CBDatabaseAdmin_tableMetadataList', CBDatabaseAdmin::fetchTableMetadataList()],
        ];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [Colby::flexpath(__CLASS__, 'v435.js', cbsysurl())];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBUI',
            'CBUISectionItem4',
            'CBUIStringsPart',
        ];
    }

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $spec = CBModels::fetchSpecByID(CBDevelopAdminMenu::ID());

        $spec->items[] = (object)[
            'className' => 'CBMenuItem',
            'name' => 'database',
            'text' => 'Database',
            'URL' => '/admin/?c=CBDatabaseAdmin',
        ];

        CBDB::transaction(function () use ($spec) {
            CBModels::save($spec);
        });
    }

    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return ['CBDevelopAdminMenu'];
    }

    /**
     * @return [object]
     */
    private static function fetchTableMetadataList(): array {
        $SQL = <<<EOT

            SELECT  table_name as tableName,
                    round(((data_length + index_length) / 1000 / 1000), 2) AS tableSizeInMB
            FROM    information_schema.TABLES
            WHERE   table_schema = DATABASE()

EOT;

        return CBDB::SQLToObjects($SQL);
    }
}
