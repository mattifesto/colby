<?php

final class CBModelAssociationsTable {

    /**
     * @TODO 2018_09_14
     *
     *      Add the following key to the table:
     *
     *      KEY associatedID_className (associatedID, className)
     *
     * @return void
     */
    static function CBInstall_install(): void {
        $SQL = <<<EOT

            CREATE TABLE IF NOT EXISTS CBModelAssociations (
                ID              BINARY(20) NOT NULL,
                className       VARCHAR(80) NOT NULL,
                associatedID    BINARY(20) NOT NULL,

                PRIMARY KEY (ID, className, associatedID)
            )
            ENGINE=InnoDB
            DEFAULT CHARSET=utf8mb4
            COLLATE=utf8mb4_unicode_520_ci

EOT;

        Colby::query($SQL);
    }
}
