<?php

final class CBUsers {

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $SQL = <<<EOT

            CREATE TABLE IF NOT EXISTS `ColbyUsers` (
                `id`                            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `hash`                          BINARY(20) NOT NULL,
                `facebookId`                    BIGINT UNSIGNED NOT NULL,
                `facebookAccessToken`           VARCHAR(255),
                `facebookAccessExpirationTime`  INT UNSIGNED,
                `facebookName`                  VARCHAR(100) NOT NULL,
                `facebookFirstName`             VARCHAR(50) NOT NULL,
                `facebookLastName`              VARCHAR(50) NOT NULL,
                `facebookTimeZone`              TINYINT NOT NULL DEFAULT '0',
                `hasBeenVerified`               BIT(1) NOT NULL DEFAULT b'0',

                PRIMARY KEY (`id`),
                UNIQUE KEY `facebookId` (`facebookId`),
                UNIQUE KEY `hash` (`hash`),
                KEY `hasBeenVerified_facebookLastName` (`hasBeenVerified`, `facebookLastName`)
            )
            ENGINE=InnoDB
            DEFAULT CHARSET=utf8mb4
            COLLATE=utf8mb4_unicode_520_ci

EOT;

        Colby::query($SQL);

        CBUsers::installUserGroup('Administrators');
        CBUsers::installUserGroup('Developers');
    }

    /**
     * @param string $name
     *
     *      The name of the group such as 'Administrators' or
     *      'LEWholesaleCustomers'.
     *
     * @return void
     */
    static function installUserGroup(string $name): void {
        $SQL = <<<EOT

            CREATE TABLE IF NOT EXISTS `ColbyUsersWhoAre{$name}` (
                `userId`    BIGINT UNSIGNED NOT NULL,
                `added`     DATETIME NOT NULL,

                PRIMARY KEY (`userId`),
                CONSTRAINT `ColbyUsersWhoAre{$name}_userId`
                    FOREIGN KEY (`userId`)
                    REFERENCES `ColbyUsers` (`id`)
                    ON DELETE CASCADE
            )
            ENGINE=InnoDB
            DEFAULT CHARSET=utf8mb4
            COLLATE=utf8mb4_unicode_520_ci

EOT;

        Colby::query($SQL);
    }
}
