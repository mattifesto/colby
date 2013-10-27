<?php

/**
 * 2013.04.29
 *
 * This upgrade adds the `thumbnailURL` column to the `ColbyPages` table. This
 * column will allow thumbnails for documents to be of any type and in any
 * location. Before this upgrade, thumbnails were always assumed to have a
 * filename of `thumbnail.jpg` and be located in the archive's data directory.
 */

/**
 * Determine whether the upgrade is needed.
 */

$sql = <<<EOT
SELECT
    COUNT(*) as `thumbnailURLColumnExists`
FROM
    information_schema.COLUMNS
WHERE
    TABLE_SCHEMA = DATABASE() AND
    TABLE_NAME = 'ColbyPages' AND
    COLUMN_NAME = 'thumbnailURL'
EOT;

$result = Colby::query($sql);

$thumbnailURLColumnExists = $result->fetch_object()->thumbnailURLColumnExists;

$result->free();

if ($thumbnailURLColumnExists)
{
    return;
}

$upgradeQueries = array();

/**
 * Add the `thumbnailURL` column to the `ColbyPages` table.
 */

$upgradeQueries[] = <<<EOT
ALTER TABLE
    `ColbyPages`
ADD
    `thumbnailURL` VARCHAR(200)
AFTER
    `subtitleHTML`
EOT;

/**
 * Execute the upgrade.
 */

foreach ($upgradeQueries as $sql)
{
    Colby::query($sql);
}
