<?php

/**
 * 2013.01.20
 *
 * This upgrade exists because we were using the archiveId (BINARY(20)) as
 * the primary key for the ColbyPages table. After much thought and research
 * it's been determined that each table should have a primary key of type:
 *
 *      BIGINT UNSIGNED NOT NULL AUTO_INCREMENT
 *
 * to speed up foreign key operations.
 *
 * So this upgrade takes the following steps:
 *
 * 1.  If the `id` column already exists on the ColbyPages table, exit the
 *     upgrade; otherwise continue.
 *
 * 2.  Drop the table's primary key so that the `archiveId` column is now just
 *     a regular column.
 *
 * 3.  Add a unique index to the `archiveId` column so that its values stay
 *     unique even though it's no longer the primary key.
 *
 * 4.  Create the new `id` column as the primary key. The current rows will
 *     all automatically get incrementing values for this column and future
 *     rows will be able to be added without issue.
 *
 * Note:
 *  Stop including this code from the database setup handler after all existing
 *  installations have been updated. However, it's probably good to keep the
 *  file around for historical and informational purposes.
 */

$sql = <<<EOT
SELECT
    COUNT(*) as `count`
FROM
    information_schema.COLUMNS
WHERE
    TABLE_SCHEMA=DATABASE() AND
    TABLE_NAME='ColbyPages' AND
    COLUMN_NAME='id'
EOT;

$result = Colby::query($sql);

$count = $result->fetch_object()->count;

$result->free();

if (1 == $count)
{
    return;
}

$upgradeQueries = array();

$upgradeQueries[] = <<<EOT
ALTER TABLE
    `ColbyPages`
DROP PRIMARY KEY;
EOT;

$upgradeQueries[] = <<<EOT
ALTER TABLE
    `ColbyPages`
ADD UNIQUE KEY `archiveId` (`archiveId`);
EOT;

$upgradeQueries[] = <<<EOT
ALTER TABLE
    `ColbyPages`
ADD `id` BIGINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT FIRST;
EOT;

foreach ($upgradeQueries as $sql)
{
    Colby::query($sql);
}
