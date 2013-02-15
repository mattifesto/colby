<?php

/**
 * 2013.01.31
 *
 * It will have been determined that the database needs to be installed before
 * this file is included.
 */

/**
 * This is the current uninstall SQL:
 *

DROP TABLE `ColbyPages`;
DROP TABLE `ColbyUsersWhoAreAdministrators`;
DROP TABLE `ColbyUsersWhoAreDevelopers`;
DROP TABLE `ColbyUsers`;
DROP PROCEDURE `ColbyVerifyUser`;
DROP FUNCTION `ColbySchemaVersionNumber`;

 */


$sqls = array();

/**
 * Make sure the database settings are correct.
 *
 * The database should be created with these settings. In the case of hosted
 * MySQL, however, it may not be an option when creating the database.
 */
$sqls[] = <<<EOT
ALTER DATABASE
DEFAULT CHARSET=utf8
COLLATE=utf8_unicode_ci
EOT;

/**
 * Create the `ColbyUsers` table.
 */
$sqls[] = <<<EOT
CREATE TABLE IF NOT EXISTS `ColbyUsers`
(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `hash` BINARY(20) NOT NULL,
    `facebookId` BIGINT UNSIGNED NOT NULL,
    `facebookAccessToken` VARCHAR(255),
    `facebookAccessExpirationTime` INT UNSIGNED,
    `facebookName` VARCHAR(100) NOT NULL,
    `facebookFirstName` VARCHAR(50) NOT NULL,
    `facebookLastName` VARCHAR(50) NOT NULL,
    `facebookTimeZone` TINYINT NOT NULL DEFAULT '0',
    `hasBeenVerified` BIT(1) NOT NULL DEFAULT b'0',
    PRIMARY KEY (`id`),
    UNIQUE KEY `facebookId` (`facebookId`),
    UNIQUE KEY `hash` (`hash`),
    KEY `hasBeenVerified_facebookLastName` (`hasBeenVerified`, `facebookLastName`)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8
COLLATE=utf8_unicode_ci
EOT;

/**
 * Create the `ColbyUsersWhoAreAdministrators` table.
 */
$sqls[] = <<<EOT
CREATE TABLE IF NOT EXISTS `ColbyUsersWhoAreAdministrators`
(
    `userId` BIGINT UNSIGNED NOT NULL,
    `added` DATETIME NOT NULL,
    PRIMARY KEY (`userId`),
    CONSTRAINT `ColbyUsersWhoAreAdministrators_userId`
        FOREIGN KEY (`userId`)
        REFERENCES `ColbyUsers` (`id`)
        ON DELETE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8
COLLATE=utf8_unicode_ci
EOT;

/**
 * Create the `ColbyUsersWhoAreDevelopers` table.
 */
$sqls[] = <<<EOT
CREATE TABLE IF NOT EXISTS `ColbyUsersWhoAreDevelopers`
(
    `userId` BIGINT UNSIGNED NOT NULL,
    `added` DATETIME NOT NULL,
    PRIMARY KEY (`userId`),
    CONSTRAINT `ColbyUsersWhoAreDevelopers_userId`
        FOREIGN KEY (`userId`)
        REFERENCES `ColbyUsers` (`id`)
        ON DELETE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8
COLLATE=utf8_unicode_ci
EOT;

/**
 * Create the `ColbyPages` table.
 */
$sqls[] = <<<EOT
CREATE TABLE IF NOT EXISTS `ColbyPages`
(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `archiveId` BINARY(20) NOT NULL,
    `modelId` BINARY(20),
    `viewId` BINARY(20),
    `groupId` BINARY(20),
    `stub` VARCHAR(100) NOT NULL,
    `titleHTML` VARCHAR(150) NOT NULL,
    `subtitleHTML` VARCHAR(150) NOT NULL,
    `searchText` LONGTEXT,
    `published` DATETIME,
    `publishedBy` BIGINT UNSIGNED,
    PRIMARY KEY (`id`),
    UNIQUE KEY `archiveId` (`archiveId`),
    UNIQUE KEY `stub` (`stub`),
    KEY `groupId_published` (`groupId`, `published`),
    CONSTRAINT `ColbyPages_publishedBy`
        FOREIGN KEY (`publishedBy`)
        REFERENCES `ColbyUsers` (`id`)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8
COLLATE=utf8_unicode_ci
EOT;

/**
 * Create the `ColbyVerifyUser` table.
 */
$sqls[] = <<<EOT
CREATE PROCEDURE ColbyVerifyUser(IN userId BIGINT UNSIGNED)
BEGIN
    UPDATE `ColbyUsers`
    SET
        `hasBeenVerified` = b'1'
    WHERE
        `id` = userId;
END
EOT;

/**
 * Heredocs won't parse constants so the version number must be placed
 * in a variable.
 */
$versionNumber = COLBY_VERSION_NUMBER;

/**
 * ColbySchemaVersionNumber
 */
$sqls[] = <<<EOT
CREATE FUNCTION ColbySchemaVersionNumber()
RETURNS BIGINT UNSIGNED
BEGIN
    RETURN {$versionNumber};
END
EOT;


/**
 * Run setup queries
 */
foreach ($sqls as $sql)
{
    Colby::query($sql);
}
