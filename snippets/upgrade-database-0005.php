<?php

/**
 * This upgrade adds the `ColbyUsersWhoAreDevelopers` table.
 */

/**
 * Determine whether the `ColbyUsersWhoAreDevelopers` already exists.
 */

$sql = <<<EOT
SELECT
    COUNT(*) as `count`
FROM
    information_schema.TABLES
WHERE
    TABLE_SCHEMA = DATABASE() AND
    TABLE_NAME = 'ColbyUsersWhoAreDevelopers'
EOT;

$result = Colby::query($sql);

$count = $result->fetch_object()->count;

$result->free();

if (1 == $count)
{
    return;
}

/**
 * The website has the opportunity to add its own snippet for this upgrade
 * in which it can define callbacks for work it needs to do before and after
 * the upgrade if an upgrade needs to happen. After defining these callbacks
 * web website's snippet will include this file.
 *
 * Call the pre upgrade callback.
 */

if (is_callable('doPreUpgradeDatabase0005'))
{
    if (false === doPreUpgradeDatabase0005())
    {
        return false;
    }
}

/**
 * Create the `ColbyUsersWhoAreDevelopers` table.
 */

$sql = <<<EOT
CREATE TABLE `ColbyUsersWhoAreDevelopers`
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

Colby::query($sql);

/**
 * Call the post upgrade callback.
 */

if (is_callable('doPostUpgradeDatabase0005'))
{
    doPostUpgradeDatabase0005();
}
