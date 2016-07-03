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


/**
 * Make sure the database settings are correct.
 *
 * The database should be created with these settings. In the case of hosted
 * MySQL, however, it may not be an option when creating the database.
 */

$SQL = <<<EOT

    ALTER DATABASE
    DEFAULT CHARSET=utf8
    COLLATE=utf8_unicode_ci

EOT;

Colby::query($SQL);


CBTasks::install();
CBUsers::install();
CBModels::install();
CBModelsPreferences::install();
CBPages::install();
CBSitePreferences::install();
CBStandardModels::install();
CBThemedTextView::install(); /* deprecated */
CBImages::install();
CBRequestTracker::install(); /* deprecated */
CBWellKnownThemeForContent::install();
CBWellKnownThemeForPageTitleAndDescription::install();
CBWellKnownPageForTestingPageTitleAndBodyText::install();

$SQL = <<<EOT

    CREATE TABLE IF NOT EXISTS `CBDictionary` (
        `key`                   VARCHAR(100),
        `valueJSON`             LONGTEXT,
        `number`                BIGINT UNSIGNED NOT NULL DEFAULT 1,

        PRIMARY KEY (`key`)
    )
    ENGINE=InnoDB
    DEFAULT CHARSET=utf8
    COLLATE=utf8_unicode_ci

EOT;

Colby::query($SQL);

// 2015.10.26
CBUpgradesForVersion172::run();

// 2015.11.12
CBUpgradesForVersion174::run();

// 2015.12.28
CBUpgradesForVersion178::run();

// 2016.02.10
CBUpgradesForVersion183::run();

// 2016.03.15
CBUpgradesForVersion188::run();

// 2016.04.28
CBUpgradesForVersion191::run();

/**
 *
 */

$tuple = CBDictionaryTuple::initWithKey('CBSystemVersionNumber');
$tuple->value = CBSystemVersionNumber;
$tuple->update();

$tuple = CBDictionaryTuple::initWithKey('CBSiteVersionNumber');
$tuple->value = CBSiteVersionNumber;
$tuple->update();
