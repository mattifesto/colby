<?php

final class
CBModels2Table {

    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function
    CBInstall_install(
    ): void {
        CBModels2Table::create();
    }
    /* CBInstall_install() */



    /* -- functions -- -- -- -- -- */



    /**
     * Creates either the permanent or a temporary CBModels2 table.
     *
     * @param bool $temporary
     *
     * @return void
     */
    private static function
    create(
    ): void {
        $SQL = <<<EOT

            CREATE TABLE
            IF NOT EXISTS
            CBModels2_table

            (
                CBModels2_CBID_column
                BINARY(20) NOT NULL,

                CBModels2_className_column
                VARCHAR(80) NOT NULL,

                CBModels2_created_column
                BIGINT NOT NULL,

                CBModels2_modified_column
                BIGINT NOT NULL,

                CBModels2_version_column
                BIGINT UNSIGNED NOT NULL,

                CBModels2_searchText_column
                LONGTEXT NOT NULL,

                CBModels2_URLPath_column
                VARCHAR(100) NOT NULL,

                PRIMARY KEY (
                    CBModels2_CBID_column
                ),

                KEY CBModels2_className_created_key (
                    CBModels2_className_column,
                    CBModels2_created_column
                ),

                KEY CBModels2_className_modified_key (
                    CBModels2_className_column,
                    CBModels2_modified_column
                ),

                KEY CBModels2_URLPath_created_key (
                    CBModels2_URLPath_column,
                    CBModels2_created_column
                )
            )

            ENGINE=InnoDB
            DEFAULT CHARSET=utf8mb4
            COLLATE=utf8mb4_unicode_520_ci

        EOT;

        Colby::query(
            $SQL
        );
    }
    /* create() */

}
