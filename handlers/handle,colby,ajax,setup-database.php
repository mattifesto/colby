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

$sqls[] = <<<EOT
ALTER DATABASE
DEFAULT CHARSET=utf8
COLLATE=utf8_unicode_ci
EOT;

$sqls[] = <<<EOT
CREATE TABLE IF NOT EXISTS `ColbyUsers`
(
    `id` BIGINT UNSIGNED NOT NULL,
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

$sqls[] = <<<EOT
CREATE TABLE IF NOT EXISTS `ColbyBlogPosts`
(
    `id` BINARY(20) NOT NULL,
    `type` BINARY(20) NOT NULL,
    `stub` VARCHAR(119) NOT NULL,
    `titleHTML` VARCHAR(119) NOT NULL,
    `subtitleHTML` VARCHAR(119),
    `published` DATETIME,
    `publishedBy` BIGINT UNSIGNED,
    PRIMARY KEY (`id`),
    UNIQUE KEY `stub` (`stub`),
    KEY `published` (`published`),
    CONSTRAINT `ColbyBlogPosts_publishedBy` FOREIGN KEY (`publishedBy`)
        REFERENCES `ColbyUsers` (`id`)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8
COLLATE=utf8_unicode_ci
EOT;

$sqls[] = <<<EOT
CREATE TABLE IF NOT EXISTS `ColbySequences`
(
  `name` VARCHAR(50) NOT NULL,
  `id` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`name`)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8
COLLATE=utf8_unicode_ci
EOT;

$sqls[] = <<<EOT
CREATE PROCEDURE ColbyCreateSequence(IN sequenceName VARCHAR(50))
BEGIN
    INSERT IGNORE INTO `ColbySequences`
        (
            `name`,
            `id`
        )
        VALUES
        (
            sequenceName,
            '0'
        );
END
EOT;

$sqls[] = <<<EOT
CREATE FUNCTION ColbyGetNextInsertIdForSequence
(
    theSequenceName VARCHAR(50)
)
RETURNS BIGINT UNSIGNED
BEGIN
    UPDATE `ColbySequences`
    SET
        `id` = LAST_INSERT_ID(`id` + 1)
    WHERE
        `name` = theSequenceName;

    RETURN LAST_INSERT_ID();
END
EOT;

$sqls[] = <<<EOT
CREATE FUNCTION ColbyGetUserIdWithFacebookId
(
    theFacebookId BIGINT UNSIGNED
)
RETURNS BIGINT UNSIGNED
BEGIN
    DECLARE theUserId BIGINT UNSIGNED DEFAULT NULL;
    DECLARE CONTINUE HANDLER FOR NOT FOUND BEGIN END;

    SELECT
        `id` INTO theUserId
    FROM
        `ColbyUsers`
    WHERE
        `facebookId` = theFacebookId;

    RETURN theUserId;
END
EOT;

$sqls[] = <<<EOT
CREATE FUNCTION ColbyLoginFacebookUser
(
    theFacebookId BIGINT UNSIGNED,
    theFacebookAccessToken VARCHAR(255),
    theFacebookAccessExpirationTime INT UNSIGNED,
    theFacebookName VARCHAR(100),
    theFacebookFirstName VARCHAR(50),
    theFacebookLastName VARCHAR(50),
    theFacebookTimeZone TINYINT
)
RETURNS BIGINT UNSIGNED
BEGIN
    DECLARE theUserId BIGINT UNSIGNED DEFAULT NULL;

    SELECT
        IFNULL
        (
            ColbyGetUserIdWithFacebookId(theFacebookId),
            ColbyGetNextInsertIdForSequence('ColbyUsersId')
        )
    INTO
        theUserId;

    INSERT INTO `ColbyUsers`
    (
        `id`,
        `facebookId`,
        `facebookAccessToken`,
        `facebookAccessExpirationTime`,
        `facebookName`,
        `facebookFirstName`,
        `facebookLastName`,
        `facebookTimeZone`
    )
    VALUES
    (
        theUserId,
        theFacebookId,
        theFacebookAccessToken,
        theFacebookAccessExpirationTime,
        theFacebookName,
        theFacebookFirstName,
        theFacebookLastName,
        theFacebookTimeZone
    )
    ON DUPLICATE KEY UPDATE
        `facebookAccessToken` = theFacebookAccessToken,
        `facebookAccessExpirationTime` = theFacebookAccessExpirationTime,
        `facebookName` = theFacebookName,
        `facebookFirstName` = theFacebookFirstName,
        `facebookLastName` = theFacebookLastName,
        `facebookTimeZone` = theFacebookTimeZone;

    RETURN theUserId;
END
EOT;

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

// heredocs don't parse for constants so place the version in a variable
$version = COLBY_VERSION;

$sqls[] = <<<EOT
CREATE FUNCTION ColbyVersion()
RETURNS VARCHAR(15)
BEGIN
    RETURN '{$version}';
END
EOT;

$sqls[] = <<<EOT
CALL ColbyCreateSequence('ColbyUsersId')
EOT;

foreach ($sqls as $sql)
{
    Colby::query($sql);
}

$response->wasSuccessful = true;
$response->message = 'The database schema was updated successfully.';

$response->end();
