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
        $strings[$j] = Colby::mysqli()->escape_string("This the string with index {$j}.");

        $j++;
    }

    $rowId = $i + 100000;

    $sql = <<<EOT
INSERT INTO `TestMySQLvsColbyArchive`
(
    `rowId`,
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
    '{$rowId}',
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

$response->send();
