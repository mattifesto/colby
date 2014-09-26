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
DROP FUNCTION IF EXISTS `ColbySchemaVersionNumber`;
DROP FUNCTION IF EXISTS `ColbySiteSchemaVersionNumber`;

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
 * `ColbyPages` organizes pages by "group", although a page does not need to
 * have a group. Groups facilitate creating lists of pages for specific and
 * common purposes, for instance, a list of blog posts or press releases. A
 * simple independent page such as an "about" page would most likely not have
 * a group.
 *
 * The purpose of the columns of `ColbyPages` is to have enough data to
 * facilitate quickly creating simple lists of pages and a site map. The table
 * contains fields for titleHTML, subtitleHTML, thumbnailURL, published,
 * publishedYearMonth, and publishedBy. If any group specific information
 * is needed for each page, an additional data store such as a group specific
 * table or an archive should be created.
 *
 * The `keyValueData` schema for this table:
 *
 *      schema              "ColbyPage"
 *      schemaVersion:      1
 *      groupKeyValueData:  <key-value data>
 *      typeKeyValueData:   <key-value data>
 *
 * The group and type key-value data properties can hold key-value data related
 * to the group and type. Any other necessary data should be kept in an
 * additional data store such as a group specific table or an archive.
 *
 * The primary extensibility method for this table is to create another domain
 * specific table. Creating domain specific tables is encouraged because it
 * reduces the need for backward compatibility.
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
        `typeID`                BINARY(20),
        `groupID`               BINARY(20),
        `URI`                   VARCHAR(100),
        `titleHTML`             TEXT NOT NULL,
        `subtitleHTML`          TEXT NOT NULL,
        `thumbnailURL`          VARCHAR(200),
        `searchText`            LONGTEXT,
        `published`             BIGINT,
        `publishedYearMonth`    CHAR(6) NOT NULL DEFAULT '',
        `publishedBy`           BIGINT UNSIGNED,
        PRIMARY KEY (`ID`),
        UNIQUE KEY `archiveID` (`archiveID`),
        UNIQUE KEY `stub` (`URI`),
        KEY `groupID_published` (`groupID`, `published`),
        KEY `groupID_publishedYearMonth_published` (`groupID`, `publishedYearMonth`, `published`),
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
        `typeID`                BINARY(20),
        `groupID`               BINARY(20),
        `URI`                   VARCHAR(100),
        `titleHTML`             TEXT NOT NULL,
        `subtitleHTML`          TEXT NOT NULL,
        `thumbnailURL`          VARCHAR(200),
        `searchText`            LONGTEXT,
        `published`             BIGINT,
        `publishedYearMonth`    CHAR(6) NOT NULL DEFAULT '',
        `publishedBy`           BIGINT UNSIGNED,
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


/**
 * 2014.08.25
 *  There is an issue with MySQL stored functions that makes some web hosting
 *  companies disallow their use. The function `ColbySchemaVersionNumber` has
 *  been replaced by the `CBSystemVersionNumber` value in the `CBDictionary`
 *  table. This SQL statement can be removed after all sites have been updated.
 */

$sqls[] = <<<EOT

    DROP FUNCTION IF EXISTS `ColbySchemaVersionNumber`;

EOT;


/**
 * `ColbySiteSchemaVersionNumber` has been replaced by the `CBSiteVersionNumber`
 * value in the `CBDictionary` table.
 */

$sqls[] = <<<EOT

    DROP FUNCTION IF EXISTS `ColbySiteSchemaVersionNumber`;

EOT;


/**
 *
 */

foreach ($sqls as $sql)
{
    Colby::query($sql);
}


/**
 *
 */

$tuple = CBDictionaryTuple::initWithKey('CBSystemVersionNumber');
$tuple->value = CBSystemVersionNumber;
$tuple->update();

$tuple = CBDictionaryTuple::initWithKey('CBSiteVersionNumber');
$tuple->value = CBSiteVersionNumber;
$tuple->update();
