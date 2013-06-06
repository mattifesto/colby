<?php

$response = new ColbyOutputManager('ajax-response');
$response->begin();

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    $response->message = 'You do not have authorization to perform this action.';

    goto done;
}

$sqls = array();

/**
 * Remove the test table. It should only exist if a previous test attempt
 * failed.
 */

$sqls[] = <<<EOT
DROP TABLE IF EXISTS `TestMySQLvsColbyArchive`
EOT;

/**
 * Create the test table.
 */

$sqls[] = <<<EOT
CREATE TABLE `TestMySQLvsColbyArchive`
(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `field0` VARCHAR(200),
    `field1` VARCHAR(200),
    `field2` VARCHAR(200),
    `field3` VARCHAR(200),
    `field4` VARCHAR(200),
    `field5` VARCHAR(200),
    `field6` VARCHAR(200),
    `field7` VARCHAR(200),
    `field8` VARCHAR(200),
    `field9` VARCHAR(200),
    PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8
COLLATE=utf8_unicode_ci
EOT;

/**
 * Run the queries.
 */

foreach ($sqls as $sql)
{
    Colby::query($sql);
}

/**
 * Delete archives
 */

$countOfArchives = 5000;

$i = 0;

while ($i < $countOfArchives)
{
    $archiveId = sha1("This is ColbyArchive performance test number {$i}.");

    ColbyArchive::deleteArchiveWithArchiveId($archiveId);

    $i++;
}

/**
 * Send the response.
 */

$response->wasSuccessful = true;
$response->message = 'Test environment ready.';

done:

$response->end();
