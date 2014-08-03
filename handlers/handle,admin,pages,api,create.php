<?php

if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed-ajax.php';
}

$response = new CBAjaxResponse();

/**
 *
 */

$dataStoreID        = $_POST['data-store-id'];
$rowData            = CBPages::insertRow($dataStoreID);
$response->rowID    = $rowData->rowID;


/**
 *
 */

$dataStore = new CBDataStore($dataStoreID);
$dataStore->makeDirectory();


/**
 * Send the response
 */

$response->wasSuccessful = true;

$response->send();
