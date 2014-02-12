<?php

include_once CBSystemDirectory . '/classes/CBDataStore.php';


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

$dataStore          = new CBDataStore($dataStoreID);
$dataStoreDirectory = $dataStore->directory();
$modelFilename      = "{$dataStoreDirectory}/model.json";

if (file_exists($modelFilename))
{
    $modelJSON = file_get_contents($modelFilename);

    $response->modelJSON = $modelJSON;
}


/**
 * Send the response
 */

$response->wasSuccessful = true;

done:

$response->end();
