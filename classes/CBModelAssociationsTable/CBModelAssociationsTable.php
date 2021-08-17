<?php

final class
CBModelAssociationsTable {

    /**
     * @NOTE 2018_12_07
     *
     *      The "className" column will eventually be renamed to
     *      "associationKey" because it does not represent an actual class name
     *      but instead the context of the association for the row.
     *
     * @return void
     */
    static function
    CBInstall_install(
    ): void {
        $SQL = <<<EOT

            CREATE TABLE IF NOT EXISTS
            CBModelAssociations (
                ID              BINARY(20) NOT NULL,
                className       VARCHAR(80) NOT NULL,
                associatedID    BINARY(20) NOT NULL,

                PRIMARY KEY                 (ID, className, associatedID),
                KEY className_associatedID  (className, associatedID),
                KEY associatedID            (associatedID)
            )
            ENGINE=InnoDB
            DEFAULT CHARSET=utf8mb4
            COLLATE=utf8mb4_unicode_520_ci

        EOT;

        Colby::query(
            $SQL
        );
    }
    /* CBInstall_install() */

}
