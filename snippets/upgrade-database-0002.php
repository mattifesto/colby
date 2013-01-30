<?php

/**
 * 2013.01.29
 *
 * This upgrade exists to change the `id` column of the `ColbyUsers` table
 * to be AUTO_INCREMENT. The reason for this is that the concept of sequences
 * is no longer the best method of assigning key fields to tables.
 *
 * For the record, the current best method is to use a unique value like a hash
 * when an identifier is needed before deciding whether to create a table row.
 * If it's decided that a table row should be created the `id` will be assigned
 * via AUTO_INCREMENT and the hash will be the generated hash. Both will exist.
 *
 * This upgrade takes the following steps:
 *
 * 1.   Any procedures or functions relating to creating sequences will be
 *      removed as they always are during any database refresh. Since they
 *      have been removed from the database setup script (which includes this
 *      file) they will not be re-created.
 *
 * 2.   This file will see if the `ColbySequences` table exists. If it does that
 *      will be the evidence that this upgrade is required. If it does not,
 *      this file will return to the includer.
 *
 * 3.   The `ColbySequences` table will be dropped.
 *
 * 4.   The foreign key constraint on the `publishedBy` column of the
 *      `ColbyPages` table will be removed.
 *
 * 5.   The `id` column of the ColbyUsers table will be altered to include
 *      AUTO_INCREMENT.
 *
 * 6.   The foreign key constraint on the `publishedBy` column of the
 *      `ColbyPages` table will be re-added.
 *
 * Note:
 *  As usual once all existing installations have been updated it will be a
 *  good idea to stop including this file. It should always be kept around for
 *  historical and informational purposes.
 */

$sql = <<<EOT
SELECT
    COUNT(*) as `count`
FROM
    information_schema.TABLES
WHERE
    TABLE_SCHEMA = DATABASE() AND
    TABLE_NAME = 'ColbySequences'
EOT;

$result = Colby::query($sql);

$count = $result->fetch_object()->count;

$result->free();

if (0 == $count)
{
    return;
}

$upgradeQueries = array();

$upgradeQueries[] = <<<EOT
DROP TABLE
    `ColbySequences`
EOT;

$upgradeQueries[] = <<<EOT
ALTER TABLE
    `ColbyPages`
DROP FOREIGN KEY
    `ColbyPages_publishedBy`
EOT;

$upgradeQueries[] = <<<EOT
ALTER TABLE
    `ColbyUsers`
MODIFY
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT
EOT;

$upgradeQueries[] = <<<EOT
ALTER TABLE
    `ColbyPages`
ADD CONSTRAINT
    `ColbyPages_publishedBy`
FOREIGN KEY
    (`publishedBy`)
REFERENCES
    `ColbyUsers` (`id`)
EOT;

foreach ($upgradeQueries as $sql)
{
    Colby::query($sql);
}
