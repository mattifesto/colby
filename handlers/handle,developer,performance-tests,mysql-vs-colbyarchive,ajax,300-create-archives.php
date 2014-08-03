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
    $strings = array();

    $j = 0;

    while ($j < 10)
    {
        $strings[$j] = "This the string with index {$j}.";

        $j++;
    }

    $archiveId = sha1("This is ColbyArchive performance test number {$i}.");

    $archive = ColbyArchive::open($archiveId);

    $archive->setStringValueForKey($strings[0], 'field0');
    $archive->setStringValueForKey($strings[1], 'field1');
    $archive->setStringValueForKey($strings[2], 'field2');
    $archive->setStringValueForKey($strings[3], 'field3');
    $archive->setStringValueForKey($strings[4], 'field4');
    $archive->setStringValueForKey($strings[5], 'field5');
    $archive->setStringValueForKey($strings[6], 'field6');
    $archive->setStringValueForKey($strings[7], 'field7');
    $archive->setStringValueForKey($strings[8], 'field8');
    $archive->setStringValueForKey($strings[9], 'field9');

    $archive->save();

    $archive = null;

    $i++;
}

$duration = number_format(microtime(true) - $beginTime, 6);

/**
 * Send the response.
 */

$response->wasSuccessful = true;
$response->message = "Created {$countOfRecords} archives. Test duration: {$duration} seconds.";

$response->send();
