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
$response->message = 'Test environment cleaned up.';

done:

$response->end();
