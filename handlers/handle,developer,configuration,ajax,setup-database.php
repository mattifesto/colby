<?php

// This ajax does not require a verified user, so it must either run only when appropriate or be non-destructive.

$response = ColbyOutputManager::beginAjaxResponse();

// drop all procedures and functions

$sqls = array();

$sqls[] = <<<EOT
SELECT CONCAT('DROP PROCEDURE ', routine_name) AS `sql`
FROM information_schema.routines
WHERE routine_schema = DATABASE()
  AND routine_type = 'PROCEDURE'
EOT;

$sqls[] = <<<EOT
SELECT CONCAT('DROP FUNCTION ', routine_name) AS `sql`
FROM information_schema.routines
WHERE routine_schema = DATABASE()
  AND routine_type = 'FUNCTION'
EOT;

$sqls2 = array();

foreach ($sqls as $sql)
{
    $result = Colby::query($sql);

    while ($row = $result->fetch_object())
    {
        $sqls2[] = $row->sql;
    }

    $result->free();
}

foreach ($sqls2 as $sql)
{
    Colby::query($sql);
}

// create tables, procedures, and functions

$sqls = array();

/**
 * The database should be created with these settings. In the case of hosted
 * MySQL, however, it may not be an option when creating the database.
 */
$sqls[] = <<<EOT
ALTER DATABASE
DEFAULT CHARSET=utf8
COLLATE=utf8_unicode_ci
EOT;

/**
 * ColbyUsers
 */
$sqls[] = <<<EOT
CREATE TABLE IF NOT EXISTS `ColbyUsers`
(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
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
    KEY `hasBeenVerified_facebookLastName`
        (`hasBeenVerified`, `facebookLastName`)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8
COLLATE=utf8_unicode_ci
EOT;

/**
 * ColbyPages
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
    CONSTRAINT `ColbyPages_publishedBy` FOREIGN KEY (`publishedBy`)
        REFERENCES `ColbyUsers` (`id`)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8
COLLATE=utf8_unicode_ci
EOT;

/**
 * ColbyVerifyUser
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

/**
 * Run upgrades
 */
include(COLBY_SITE_DIRECTORY . '/colby/snippets/upgrade-database-0001.php');
include(COLBY_SITE_DIRECTORY . '/colby/snippets/upgrade-database-0002.php');

/**
 * Send response
 */
$response->wasSuccessful = true;
$response->message = 'The database schema was updated successfully.';

$response->end();
