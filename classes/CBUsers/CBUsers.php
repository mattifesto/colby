<?php

final class CBUsers {

    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $SQL = <<<EOT

            CREATE TABLE IF NOT EXISTS ColbyUsers (
                id                  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                hash                BINARY(20) NOT NULL,
                email               VARCHAR(254),
                facebookId          BIGINT UNSIGNED,
                facebookName        VARCHAR(100) NOT NULL,

                PRIMARY KEY (id),

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

        Colby::query($SQL);
    }
    /* CBInstall_install() */



    /* -- functions -- -- -- -- -- */



    /**
     * Returns the number of user accounts for the website. This function mainly
     * exists to return zero before the first user is logged in to detect that
     * they are the first user and add them to CBAdministratorsUserGroup and
     * CBDevelopersUserGroup.
     *
     * @return int
     */
    static function countOfUsers(): int {
        $SQL = <<<EOT

            SELECT  COUNT(*)
            FROM    CBModels
            WHERE   className = 'CBUser';

        EOT;

        return CBConvert::valueAsInt(
            CBDB::SQLToValue($SQL)
        );
    }
    /* countOfUsers() */



    /**
     * @deprecated use CBUserGroup models
     *
     * @return void
     */
    static function installUserGroup(string $userGroupName): void {
    }



    /**
     * @param string $userGroupName
     *
     * @return void
     */
    static function uninstallUserGroup(string $userGroupName): void {
        if (CBConvert::valueAsName($userGroupName) === null) {
            throw new CBException(
                "The \$userGroupName parameter value \"{$userGroupName}\" " .
                "is not valid.",
                '',
                'fc5c564cedc9fc1a692a6a359756bb82dcf89245'
            );
        }

        Colby::query(
            "DROP TABLE IF EXISTS ColbyUsersWhoAre{$userGroupName}"
        );
    }
    /* uninstallUserGroup() */

}
