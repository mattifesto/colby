<?php

$response = new ColbyOutputManager('ajax-response');
$response->begin();

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    $response->message = 'You do not have authorization to perform this action.';

    goto done;
}

$countOfRecords = 1000;

$beginTime = microtime(true);

$i = 0;

while ($i < $countOfRecords)
{
    $strings = array();

    $j = 0;

    while ($j < 10)
    {
        $strings[$j] = Colby::mysqli()->escape_string("This the string with index {$j}.");

        $j++;
    }

    $sql = <<<EOT
INSERT INTO `TestMySQLvsColbyArchive`
(
    `field0`,
    `field1`,
    `field2`,
    `field3`,
    `field4`,
    `field5`,
    `field6`,
    `field7`,
    `field8`,
    `field9`
)
VALUES
(
    '{$strings[0]}',
    '{$strings[1]}',
    '{$strings[2]}',
    '{$strings[3]}',
    '{$strings[4]}',
    '{$strings[5]}',
    '{$strings[6]}',
    '{$strings[7]}',
    '{$strings[8]}',
    '{$strings[9]}'
)
EOT;

    Colby::query($sql);

    $i++;
}

$duration = number_format(microtime(true) - $beginTime, 6);

/**
 * Send the response.
 */

$response->wasSuccessful = true;
$response->message = "Inserted {$countOfRecords} records. Test duration: {$duration} seconds.";

done:

$response->end();
