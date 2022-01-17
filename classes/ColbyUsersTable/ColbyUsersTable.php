<?php

final class
ColbyUsersTable {

    /* -- CBInstall interfaces -- */



    /**
     * @deprecated 2021_09_25
     *
     *      The email and facebookId columns should be handled in the same way
     *      that usernames are handled. This table can then be removed.
     *
     * @return void
     */
    static function
    CBInstall_install(
    ): void {
        $SQL = <<<EOT

            CREATE TABLE
            IF NOT EXISTS
            ColbyUsers (
                id
                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

                hash
                BINARY(20) NOT NULL,

                email
                VARCHAR(254),

                facebookId
                BIGINT UNSIGNED,

                facebookName
                VARCHAR(100) NOT NULL,

                PRIMARY KEY (
                    id
                ),

                UNIQUE KEY facebookId (
                    facebookId
                ),

                UNIQUE KEY hash (
                    hash
                ),

                UNIQUE KEY email (
                    email
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
    /* CBInstall_install() */

}
