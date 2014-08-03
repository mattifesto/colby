<?php

/**
 * 2013.01.31
 *
 * This upgrade exists to add the `hash` column to the ColbyUsers table that
 * can be used an archive id for a user which is required to give each user a
 * user page.
 */

$sql = <<<EOT
SELECT
    COUNT(*) as `count`
FROM
    information_schema.COLUMNS
WHERE
    TABLE_SCHEMA = DATABASE() AND
    TABLE_NAME = 'ColbyUsers' AND
    COLUMN_NAME = 'hash'
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

if (is_callable('doPreUpgradeDatabase0003'))
{
    doPreUpgradeDatabase0003();
}

/**
 * Create the `hash` column.
 */

$sql = <<<EOT
ALTER TABLE
    `ColbyUsers`
ADD
    `hash` BINARY(20)
AFTER
    `id`
EOT;

Colby::query($sql);

/**
 * Populate the `hash` column for existing users.
 */

$sql = <<<EOT
SELECT
    `id`
FROM
    `ColbyUsers`
EOT;

$result = Colby::query($sql);

$i = 0;

while ($row = $result->fetch_object())
{
    $time = microtime();
    $rand = rand();
    $hash = sha1("time:{$time} i:{$i} rand:{$rand}");
    $hash = "'{$hash}'";
    $id = "'{$row->id}'";

    $sql = <<<EOT
UPDATE
    `ColbyUsers`
SET
    `hash` = UNHEX({$hash})
WHERE
    `id` = {$id}
EOT;

    Colby::query($sql);

    $i++;
}

$result->free();

/**
 * Modify the `hash` column to be `NOT NULL`.
 */

$sql = <<<EOT
ALTER TABLE
    `ColbyUsers`
MODIFY
    `hash` BINARY(20) NOT NULL
EOT;

Colby::query($sql);

/**
 * Add a unique index to the `hash` column.
 */

$sql = <<<EOT
ALTER TABLE
    `ColbyUsers`
ADD
    UNIQUE KEY `hash` (`hash`)
EOT;

Colby::query($sql);

/**
 * Call the post upgrade callback.
 */

if (is_callable('doPostUpgradeDatabase0003'))
{
    doPostUpgradeDatabase0003();
}
