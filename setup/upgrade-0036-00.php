<?php

/**
 * 2013.09.15
 *
 * When the ColbyPages table was first created the idea was that only documents
 * that could be viewed as web pages would be added. At the point of this
 * writing, the table is scheduled to be renamed to `ColbyDocuments` and will
 * contain a row for every valid archive whether it represents and actual web
 * page or not.
 *
 * Because of this change, it no longer makes sense for the `stub` column,
 * which is scheduled to be renamed to `uri`, to be `NOT NULL`. It makes sense
 * that there may be many documents that do not have a URI.
 *
 * This upgrade removes the `NOT NULL` attribute from the `stub` column if it
 * still exists.
 */

/**
 * Determine whether the upgrade is needed.
 */

$sql = <<<EOT
SELECT
    COUNT(*) as `stubColumnNeedsUpgrade`
FROM
    information_schema.COLUMNS
WHERE
    TABLE_SCHEMA = DATABASE() AND
    TABLE_NAME = 'ColbyPages' AND
    COLUMN_NAME = 'stub' AND
    IS_NULLABLE = 'NO'
EOT;

$result = Colby::query($sql);

$stubColumnNeedsUpgrade = $result->fetch_object()->stubColumnNeedsUpgrade;

$result->free();

if (!$stubColumnNeedsUpgrade)
{
    return;
}

/**
 * Perform upgrade.
 */

$sql = <<<EOT
ALTER TABLE
    `ColbyPages`
MODIFY
    `stub` VARCHAR(100)
EOT;

Colby::query($sql);
