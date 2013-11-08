<?php

/**
 * 2013.11.07
 *
 * This upgrade increases the general usefulness of the ColbyPages table by
 * adding two columns.
 *
 * `keyValueData`
 *
 * The latest Colby design methods indicate that most tables should have a
 * `keyValueData` column to hold data associated with a column but that isn't
 * used to index or filter rows.
 *
 * `publishedYearMonth`
 *
 * The `ColbyPages` table is designed to be able to manage creating indexes
 * for groups of documents. The most common way to organize documents at a
 * glance is by month. To do this, we need a `publishedYearMonth` column.
 */

/**
 * Determine whether the upgrade is needed.
 */

$sql = <<<EOT
SELECT
    COUNT(*) as `keyValueDataColumnExists`
FROM
    information_schema.COLUMNS
WHERE
    TABLE_SCHEMA = DATABASE() AND
    TABLE_NAME = 'ColbyPages' AND
    COLUMN_NAME = 'keyValueData'
EOT;

$result = Colby::query($sql);

$keyValueDataColumnExists = $result->fetch_object()->keyValueDataColumnExists;

$result->free();

if ($keyValueDataColumnExists)
{
    return;
}

/**
 * Add the `keyValueData` column.
 */

$sql = <<<EOT
ALTER TABLE
    `ColbyPages`
ADD COLUMN
    `keyValueData` LONGTEXT NOT NULL
AFTER
    `archiveId`
EOT;

Colby::query($sql);

/**
 * Add the `publishedYearMonth` column.
 */

$sql = <<<EOT
ALTER TABLE
    `ColbyPages`
ADD COLUMN
    `publishedYearMonth` CHAR(6) NOT NULL DEFAULT ''
AFTER
    `published`
EOT;

Colby::query($sql);

/**
 * Add index for group, publishedYearMonth, and published
 */

$sql = <<<EOT
ALTER TABLE
    `ColbyPages`
ADD KEY
    `groupId_publishedYearMonth_published` (`groupId`, `publishedYearMonth`, `published`)
EOT;

Colby::query($sql);

/**
 * Populate the `publishedYearMonth` column.
 */

$sql = <<<EOT
UPDATE
    `ColbyPages`
SET
    `publishedYearMonth` = DATE_FORMAT(FROM_UNIXTIME(`published`), '%Y%m')
EOT;

Colby::query($sql);
