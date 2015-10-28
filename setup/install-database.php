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
CBThemedTextView::install();

/**
 *
 */

foreach ($sqls as $sql) {
    Colby::query($sql);
}

CBImages::update();

// 2015.10.26
CBUpgradesForVersion172::run();

/**
 *
 */

$tuple = CBDictionaryTuple::initWithKey('CBSystemVersionNumber');
$tuple->value = CBSystemVersionNumber;
$tuple->update();

$tuple = CBDictionaryTuple::initWithKey('CBSiteVersionNumber');
$tuple->value = CBSiteVersionNumber;
$tuple->update();
