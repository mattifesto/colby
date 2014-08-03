<?php

/**
 * 2013.04.23
 *
 * This upgrade changes the only `DATETIME` type column to a `BIGINT` where
 * the a UNIX timestamp will replace the MySQL `DATETIME`.
 *
 * History:
 *
 * I have spent so much time thinking about how to store dates in MySQL and I
 * have many notes on the subject. At some point I came to the incorrect
 * conclusion that dates should be stored in UTC time in MySQL `DATETIME`
 * columns.
 *
 * For a number of reasons I did not find the following problem for some time
 * after that. Take the results of this query as an example of the problem:
 *
 *  mysql> select unix_timestamp(), utc_timestamp(), unix_timestamp(utc_timestamp());
 *  +------------------+---------------------+---------------------------------+
 *  | unix_timestamp() | utc_timestamp()     | unix_timestamp(utc_timestamp()) |
 *  +------------------+---------------------+---------------------------------+
 *  |       1366757491 | 2013-04-23 22:51:31 |                      1366782691 |
 *  +------------------+---------------------+---------------------------------+
 *
 * The question is: why doesn't the last value match the first?
 *
 * The answer is: because `UNIX_TIMESTAMP` assumes its parameter is in local
 * time and converts it to UTC time before converting it to a time stamp.
 *
 * So, how do we convert a date in UTC time to a UNIX time stamp? Well, there
 * are ways but they are all very clumsy and complex enough to waste at least
 * half a day's thinking even for advanced developers.
 *
 * Conclusion:
 *
 * The answer is to ignore MySQLs `DATETIME` type and just store UNIX time
 * stamps in BIGINT columns.
 *
 * Until now, I had strongly felt that the functionality of the tool should
 * always be used. But until now, I didn't realize how truly messed up MySQL's
 * date and time functionality is. Issues this big generally show up in large
 * organizations when nobody has ever been assigned the task of really
 * thinking things through. MySQL should move toward an 'always UTC' method
 * of storing dates and times instead of their current 'always local time'
 * method which I can't imagine works well for any database that spans multiple
 * time zones.
 *
 * Further support:
 *
 * -   Colby always uses time stamps even when sending dates to the browser. So
 *     there's never a need for MySQL to return anything else. Having a
 *     different type anywhere in the process does nothing but slow things down.
 *
 * -   Even before this, I had written off all of the MySQL date and time
 *     functions because they always seemed to lead to unclear code and bad
 *     side effects. The example above is just one more data point of that
 *     conclusion.
 *
 * -   Using UNIX time stamps leaves very little room for interpretation and
 *     therefore very little room for misunderstanding or bugs.
 *
 */

/**
 * Detect whether the upgrade is needed.
 */

$sql = <<<EOT
SELECT
    COUNT(*) as `publishedColumnIsDateTime`
FROM
    information_schema.COLUMNS
WHERE
    `TABLE_SCHEMA` = DATABASE() AND
    `TABLE_NAME` = 'ColbyPages' AND
    `COLUMN_NAME` = 'published' AND
    `DATA_TYPE` = 'datetime'
EOT;

$result = Colby::query($sql);

$publishedColumnIsDateTime = $result->fetch_object()->publishedColumnIsDateTime;

$result->free();

if (!$publishedColumnIsDateTime)
{
    return;
}

$upgradeQueries = array();

/**
 * Add the new column with a temporary name until we can delete the old
 * column and then use its name.
 */

$upgradeQueries[] = <<<EOT
ALTER TABLE
    `ColbyPages`
ADD
    `publishedTimeStamp` BIGINT
AFTER
    `published`
EOT;

/**
 * Populate the values of the new column. This will have the effect offsetting
 * them by the difference between local time and UTC time but since there are
 * so few uses of this so far, its not worth worrying about it.
 */

$upgradeQueries[] = <<<EOT
UPDATE
    `ColbyPages`
SET
    `publishedTimeStamp` = UNIX_TIMESTAMP(`published`)
EOT;

/**
 * Drop the only index that involves the `published` column so that we can
 * drop it.
 */

$upgradeQueries[] = <<<EOT
ALTER TABLE
    `ColbyPages`
DROP KEY
    `groupId_published`
EOT;

/**
 * Drop the `published` column.
 */

$upgradeQueries[] = <<<EOT
ALTER TABLE
    `ColbyPages`
DROP
    `published`
EOT;

/**
 * Rename our temporary column name to `published`.
 */

$upgradeQueries[] = <<<EOT
ALTER TABLE
    `ColbyPages`
CHANGE
    `publishedTimeStamp` `published` BIGINT
EOT;

/**
 * Replace the dropped index with a new index.
 */

$upgradeQueries[] = <<<EOT
ALTER TABLE
    `ColbyPages`
ADD KEY
    `groupId_published` (`groupId`, `published`)
EOT;

/**
 * Execute the upgrade.
 */

if (is_callable('doPreUpgradeDatabase0006'))
{
    doPreUpgradeDatabase0006();
}

foreach ($upgradeQueries as $sql)
{
    Colby::query($sql);
}

if (is_callable('doPostUpgradeDatabase0006'))
{
    doPostUpgradeDatabase0006();
}
