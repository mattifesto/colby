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
    $rowId = $i + 100000;

    $sql = <<<EOT
SELECT
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
FROM
    `TestMySQLvsColbyArchive`
WHERE
    `rowId` = '{$rowId}';
EOT;

    $result = Colby::query($sql);

    if ($result->num_rows != 1)
    {
        throw new RuntimeException("No row found for index: {$i}.");
    }

    $row = $result->fetch_object();

    $result->free();

    $field0 = $row->field0;
    $field1 = $row->field1;
    $field2 = $row->field2;
    $field3 = $row->field3;
    $field4 = $row->field4;
    $field5 = $row->field5;
    $field6 = $row->field6;
    $field7 = $row->field7;
    $field8 = $row->field8;
    $field9 = $row->field9;

    $i++;
}

$duration = number_format(microtime(true) - $beginTime, 6);

/**
 * Send the response.
 */

$response->wasSuccessful = true;
$response->message = "Read {$countOfRecords} records from the database. Test duration: {$duration} seconds.";

$response->send();
