<?php

if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed-ajax.php';
}

$response = new CBAjaxResponse();

/**
 *
 */

$dataStoreID            = $_POST['dataStoreID'];
$response->dataStoreID  = $dataStoreID;


/**
 *
 */

CBPages::recoverRowWithDataStoreIDFromTheTrash($dataStoreID);


/**
 * Send the response
 */

$response->wasSuccessful = true;

$response->send();
