<?php

final class CBModelsTable {

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        CBModelsTable::create();
    }

    /**
     * Creates either the permanent or a temporary CBModels table.
     *
     * @param bool $temporary
     *
     * @return void
     */
    static function create(bool $temporary = false): void {
        $name = $temporary ? 'CBModelsTemp' : 'CBModels';
        $options = $temporary ? 'TEMPORARY' : '';
        $SQL = <<<EOT

            CREATE {$options} TABLE IF NOT EXISTS {$name} (
                ID          BINARY(20) NOT NULL,
                className   VARCHAR(80) NOT NULL,
                created     BIGINT NOT NULL,
                modified    BIGINT NOT NULL,
                title       TEXT NOT NULL,
                version     BIGINT UNSIGNED NOT NULL,

                PRIMARY KEY                 (ID),
                KEY className_created       (className, created),
                KEY className_modified      (className, modified)
            )
            ENGINE=InnoDB
            DEFAULT CHARSET=utf8mb4
            COLLATE=utf8mb4_unicode_520_ci

EOT;

        Colby::query($SQL);
    }
}
