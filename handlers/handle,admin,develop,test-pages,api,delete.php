<?php

include_once CBSystemDirectory . '/classes/CBDataStore.php';
include_once CBSystemDirectory . '/classes/CBPages.php';


$response = new ColbyOutputManager('ajax-response');

$response->begin();

if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    $response->message = 'You are not authorized to use this feature.';

    goto done;
}

/**
 *
 */

$timeout                = time() + 20;
$dataStoreIDs           = queryForTestPageDataStoreIDs();
$processedDataStoreIDs  = array();

while (time() < $timeout &&
       $dataStoreID = array_shift($dataStoreIDs))
{
    $dataStore = new CBDataStore($dataStoreID);

    if (file_exists($dataStore->directory()))
    {
        $dataStore->delete();
    }

    $processedDataStoreIDs[] = $dataStoreID;
}

CBPages::deleteRowsWithDataStoreIDs($processedDataStoreIDs);

$countOfDeletedPages    = count($processedDataStoreIDs);
$countOfRemainingPages  = count($dataStoreIDs);
/**
 * Send the response
 */

$response->countOfDeletedPages      = $countOfDeletedPages;
$response->countOfRemainingPages    = $countOfRemainingPages;
$response->message          = "{$countOfDeletedPages} pages were deleted, {$countOfRemainingPages} remain.";
$response->wasSuccessful    = true;

done:

$response->end();

/**
 * @return array
 */
function queryForTestPageDataStoreIDs()
{
    $sql = <<<EOT

    SELECT
        LOWER(HEX(`archiveID`)) as `dataStoreID`
    FROM
        `ColbyPages`
    WHERE
        `URI` LIKE 'test42/%'

EOT;

    $result = Colby::query($sql);

    $dataStoreIDs = array();

    while ($row = $result->fetch_object())
    {
        $dataStoreIDs[] = $row->dataStoreID;
    }

    return $dataStoreIDs;
}
