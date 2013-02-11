<?php

/**
 * This upgrade adds the `ColbyUsersWhoAreAdministrators` table to move away
 * from using the `hasBeenVerified` column of the `ColbyUsers` table and away
 * from using columns of the `ColbyUsers` table to define privileges at all.
 */

/**
 * The website has the opportunity to add its own snippet for this upgrade
 * in which it can define callbacks for work it needs to do before and after
 * the upgrade if an upgrade needs to happen. After defining these callbacks
 * web website's snippet will include this file.
 *
 * Call the pre upgrade callback.
 */

if (is_callable('doPreUpgradeDatabase0004'))
{
    if (false === doPreUpgradeDatabase0004())
    {
        return false;
    }
}

/**
 * Add the `ColbyUsersWhoAreAdministrators` table.
 */

$sql = <<<EOT
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

Colby::query($sql);

/**
 * Add all of the users in the `ColbyUsers` table who are verified to the
 * `ColbyUsersWhoAreAdministrators` table.
 */

$sql = <<<EOT
INSERT INTO
    `ColbyUsersWhoAreAdministrators`
(
    `userId`,
    `added`
)
SELECT
    `id`,
    UTC_TIMESTAMP()
FROM
    `ColbyUsers`
WHERE
    `hasBeenVerified` = b'1';
EOT;

Colby::query($sql);

/**
 * Call the post upgrade callback.
 */

if (is_callable('doPostUpgradeDatabase0004'))
{
    doPostUpgradeDatabase0004();
}
