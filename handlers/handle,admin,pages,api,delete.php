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

$dataStoreID            = $_POST['dataStoreID'];
$response->dataStoreID  = $dataStoreID;


/**
 *
 */

Colby::mysqli()->autocommit(false);

CBPages::deleteRowWithDataStoreID($dataStoreID);

$dataStore = new CBDataStore($dataStoreID);
$dataStore->delete();

Colby::mysqli()->commit();
Colby::mysqli()->autocommit(true);


/**
 * Send the response
 */

$response->wasSuccessful = true;

done:

$response->end();
