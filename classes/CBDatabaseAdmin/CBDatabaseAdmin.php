<?php

final class CBDatabaseAdmin {

    /* -- CBAdmin interfaces -- -- -- -- -- */



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
        return [
            'develop',
            'database'
        ];
    }



    /**
     * @return void
     */
    static function CBAdmin_render(): void {
        CBHTMLOutput::pageInformation()->title = 'Database Administration';
    }



    /* CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [[name, value]]
     */
    static function
    CBHTMLOutput_JavaScriptVariables(
    ): array {
        return [
            [
                'CBDatabaseAdmin_tableMetadataList',
                CBDatabaseAdmin::fetchTableMetadataList()
            ],
        ];
    }
    /* CBHTMLOutput_JavaScriptVariables() */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.23.js',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBUI',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $spec = CBModels::fetchSpecByID(
            CBDevelopAdminMenu::ID()
        );

        $spec->items[] = (object)[
            'className' => 'CBMenuItem',
            'name' => 'database',
            'text' => 'Database',
            'URL' => '/admin/?c=CBDatabaseAdmin',
        ];

        CBDB::transaction(
            function () use ($spec) {
                CBModels::save($spec);
            }
        );
    }
    /* CBInstall_install() */



    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBDevelopAdminMenu'
        ];
    }



    /**
     * @return [object]
     *
     *      {
     *          table_name: string
     *          tableSizeInMB: number
     *          rowCount: int
     *      }
     */
    private static function fetchTableMetadataList(): array {
        $SQL = <<<EOT

            SELECT  table_name as tableName,
                    round(
                        ((data_length + index_length) / 1000 / 1000),
                        2
                    ) AS tableSizeInMB
            FROM    information_schema.TABLES
            WHERE   table_schema = DATABASE()

        EOT;

        $metadataForAllTables = CBDB::SQLToObjects(
            $SQL
        );

        foreach ($metadataForAllTables as $metadataForOneTable) {
            $SQL = <<<EOT

                SELECT
                    COUNT(*)
                FROM
                    {$metadataForOneTable->tableName}

            EOT;

            $metadataForOneTable->rowCount = CBDB::SQLToValue2(
                $SQL
            );
        }

        return $metadataForAllTables;
    }
    /* fetchTableMetadataList() */

}
