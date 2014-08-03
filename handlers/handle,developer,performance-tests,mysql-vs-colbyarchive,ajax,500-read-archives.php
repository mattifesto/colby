<?php

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed-ajax.php';
}

$response       = new CBAjaxResponse();
$countOfRecords = 1000;
$beginTime      = microtime(true);
$i              = 0;

while ($i < $countOfRecords)
{
    $archiveId = sha1("This is ColbyArchive performance test number {$i}.");

    $archive = ColbyArchive::open($archiveId);

    $field0 = $archive->valueForKey('field0');
    $field1 = $archive->valueForKey('field1');
    $field2 = $archive->valueForKey('field2');
    $field3 = $archive->valueForKey('field3');
    $field4 = $archive->valueForKey('field4');
    $field5 = $archive->valueForKey('field5');
    $field6 = $archive->valueForKey('field6');
    $field7 = $archive->valueForKey('field7');
    $field8 = $archive->valueForKey('field8');
    $field9 = $archive->valueForKey('field9');

    $archive = null;

    $i++;
}

$duration = number_format(microtime(true) - $beginTime, 6);

/**
 * Send the response.
 */

$response->wasSuccessful = true;
$response->message = "Read the data of {$countOfRecords} archives. Test duration: {$duration} seconds.";

$response->send();
