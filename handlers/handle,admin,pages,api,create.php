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

$dataStoreID = $_POST['data-store-id'];


/**
 *
 */

$rowData = CBPages::insertRow($dataStoreID);

$response->rowID = $rowData->rowID;


/**
 *
 */

$dataStore = new CBDataStore($dataStoreID);

$dataStore->makeDirectory();


/**
 * Send the response
 */

$response->wasSuccessful = true;

done:

$response->end();
