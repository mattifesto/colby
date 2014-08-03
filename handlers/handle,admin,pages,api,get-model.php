<?php

if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed-ajax.php';
}

$response = new CBAjaxResponse();

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

$response->send();
