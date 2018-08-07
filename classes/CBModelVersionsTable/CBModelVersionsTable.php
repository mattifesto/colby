<?php

final class CBModelVersionsTable {

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $SQL = <<<EOT

            CREATE TABLE IF NOT EXISTS `CBModelVersions`
            (
                ID              BINARY(20) NOT NULL,
                version         BIGINT UNSIGNED NOT NULL,
                modelAsJSON     LONGTEXT NOT NULL,
                specAsJSON      LONGTEXT NOT NULL,
                timestamp       BIGINT NOT NULL,

                PRIMARY KEY     (ID, version)
            )
            ENGINE=InnoDB
            DEFAULT CHARSET=utf8mb4
            COLLATE=utf8mb4_unicode_520_ci

EOT;

        Colby::query($SQL);
    }
}
