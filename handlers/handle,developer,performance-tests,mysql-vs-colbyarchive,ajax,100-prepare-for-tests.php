<?php

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed-ajax.php';
}

$response   = new CBAjaxResponse();
$sqls       = array();

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
    `rowId` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
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
    PRIMARY KEY (`rowId`)
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

$response->send();
