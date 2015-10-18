<?php

/**
 * 2014.03.27
 *
 * This file is now intended to be included during upgrades as well as
 * installations. It upgrades the system and site version numbers and allows
 * new tables to be added to only this file so that a separate upgrade file is
 * not required.
 */

/**
 * This is the current uninstall SQL:
 *

DROP TABLE `CBDictionary`;
DROP TABLE `CBPagesInTheTrash`;
DROP TABLE `ColbyPages`;
DROP TABLE `ColbyUsersWhoAreAdministrators`;
DROP TABLE `ColbyUsersWhoAreDevelopers`;
DROP TABLE `ColbyUsers`;

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
 *
 */

$sqls[] = <<<EOT

    CREATE TABLE IF NOT EXISTS `ColbyUsers`
    (
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
    DEFAULT CHARSET=utf8
    COLLATE=utf8_unicode_ci

EOT;


/**
 *
 */

$sqls[] = <<<EOT

    CREATE TABLE IF NOT EXISTS `ColbyUsersWhoAreAdministrators`
    (
        `userId`    BIGINT UNSIGNED NOT NULL,
        `added`     DATETIME NOT NULL,
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
 *
 */

$sqls[] = <<<EOT

    CREATE TABLE IF NOT EXISTS `ColbyUsersWhoAreDevelopers`
    (
        `userId`    BIGINT UNSIGNED NOT NULL,
        `added`     DATETIME NOT NULL,
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
 * The `ColbyPages` table contains one row for each page for most of the pages
 * on a website. Pages implemented entirely with PHP handlers do not need to
 * have a row in `ColbyPages` to display, but they will need to have a row to
 * be found via search or to be included in various lists of pages generated
 * using the table, including the site map.
 *
 * This table is meant to be as generically useful and unchanging as possible.
 * Changes to this table will only be made to fix bugs, simplify it, or extend
 * it where there is universal and obvious need.
 */

$sqls[] = <<<EOT

    CREATE TABLE IF NOT EXISTS `ColbyPages`
    (
        `ID`                    BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `archiveID`             BINARY(20) NOT NULL,
        `keyValueData`          LONGTEXT NOT NULL,
        `className`             VARCHAR(80),
        `classNameForKind`      VARCHAR(80),
        `typeID`                BINARY(20),
        `groupID`               BINARY(20),
        `iteration`             BIGINT UNSIGNED NOT NULL DEFAULT 1,
        `URI`                   VARCHAR(100),
        `titleHTML`             TEXT NOT NULL,
        `subtitleHTML`          TEXT NOT NULL,
        `thumbnailURL`          VARCHAR(200),
        `searchText`            LONGTEXT,
        `published`             BIGINT,
        `publishedBy`           BIGINT UNSIGNED,
        `publishedMonth`        MEDIUMINT,
        PRIMARY KEY (`ID`),
        UNIQUE KEY `archiveID` (`archiveID`),
        UNIQUE KEY `stub` (`URI`),
        KEY `classNameForKind_publishedMonth_published` (`classNameForKind`, `publishedMonth`, `published`),
        CONSTRAINT `ColbyPages_publishedBy`
            FOREIGN KEY (`publishedBy`)
            REFERENCES `ColbyUsers` (`id`)
    )
    ENGINE=InnoDB
    DEFAULT CHARSET=utf8
    COLLATE=utf8_unicode_ci

EOT;


/**
 *
 */

$sqls[] = <<<EOT

    CREATE TABLE IF NOT EXISTS `CBPagesInTheTrash`
    (
        `ID`                    BIGINT UNSIGNED NOT NULL,
        `dataStoreID`           BINARY(20) NOT NULL,
        `keyValueData`          LONGTEXT NOT NULL,
        `className`             VARCHAR(80),
        `classNameForKind`      VARCHAR(80),
        `typeID`                BINARY(20),
        `groupID`               BINARY(20),
        `iteration`             BIGINT UNSIGNED NOT NULL DEFAULT 1,
        `URI`                   VARCHAR(100),
        `titleHTML`             TEXT NOT NULL,
        `subtitleHTML`          TEXT NOT NULL,
        `thumbnailURL`          VARCHAR(200),
        `searchText`            LONGTEXT,
        `published`             BIGINT,
        `publishedBy`           BIGINT UNSIGNED,
        `publishedMonth`        MEDIUMINT,
        PRIMARY KEY (`ID`),
        UNIQUE KEY `dataStoreID` (`dataStoreID`)
    )
    ENGINE=InnoDB
    DEFAULT CHARSET=utf8
    COLLATE=utf8_unicode_ci

EOT;


/**
 *
 */

$sqls[] = <<<EOT

    CREATE TABLE IF NOT EXISTS `CBPageLists`
    (
        `pageRowID`             BIGINT UNSIGNED NOT NULL,
        `listClassName`         VARCHAR(80),
        `sort1`                 BIGINT,
        `sort2`                 BIGINT,
        PRIMARY KEY (`pageRowID`, `listClassName`),
        KEY `CBPageList` (`listClassName`, `sort1`, `sort2`),
        CONSTRAINT `CBPageListPages`
            FOREIGN KEY (`pageRowID`)
            REFERENCES `ColbyPages` (`ID`)
            ON DELETE CASCADE
    )
    ENGINE=InnoDB
    DEFAULT CHARSET=utf8
    COLLATE=utf8_unicode_ci

EOT;


/**
 *
 */

$sqls[] = <<<EOT

    CREATE TABLE IF NOT EXISTS `CBDictionary`
    (
        `key`                   VARCHAR(100),
        `valueJSON`             LONGTEXT,
        `number`                BIGINT UNSIGNED NOT NULL DEFAULT 1,
        PRIMARY KEY (`key`)
    )
    ENGINE=InnoDB
    DEFAULT CHARSET=utf8
    COLLATE=utf8_unicode_ci

EOT;

CBModels::install();
CBPages::install();
CBSitePreferences::install();
CBMainMenu::install();

/**
 *
 */

foreach ($sqls as $sql) {
    Colby::query($sql);
}

CBImages::update();

// 2015.02.11
CBUpgradesForVersion119::run();

// 2015.02.21
CBUpgradesForVersion123::run();

// 2015.03.31
CBUpgradesForVersion134::run();

// 2015.04.04
CBUpgradesForVersion136::run();

/**
 *
 */

$tuple = CBDictionaryTuple::initWithKey('CBSystemVersionNumber');
$tuple->value = CBSystemVersionNumber;
$tuple->update();

$tuple = CBDictionaryTuple::initWithKey('CBSiteVersionNumber');
$tuple->value = CBSiteVersionNumber;
$tuple->update();
