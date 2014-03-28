<?php

include_once CBSystemDirectory . '/classes/CBAjaxResponse.php';
include_once CBSystemDirectory . '/classes/CBPages.php';


$response = new CBAjaxResponse();

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

CBPages::recoverRowWithDataStoreIDFromTheTrash($dataStoreID);


/**
 * Send the response
 */

$response->wasSuccessful = true;

done:

$response->send();
