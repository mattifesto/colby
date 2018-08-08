<?php

final class CBModelVersionsTable {

    /**
     * Columns
     *
     *      timestamp (Unix timestamp)
     *
     *          This is the time the version was created.
     *
     *      replaced (Unix timestamp)
     *
     *          The value for this column is set by the CBModelPruneVersionsTask
     *          class before versions are pruned. This enables the calculation
     *          of a version's lifetime and relative historical significance.
     *
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
                replaced        BIGINT,

                PRIMARY KEY     (ID, version)
            )
            ENGINE=InnoDB
            DEFAULT CHARSET=utf8mb4
            COLLATE=utf8mb4_unicode_520_ci

EOT;

        Colby::query($SQL);
    }
}
