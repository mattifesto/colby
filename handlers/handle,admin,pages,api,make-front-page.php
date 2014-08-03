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
$frontPage              = new stdClass();
$frontPage->dataStoreID = $dataStoreID;
$frontPageJSON          = json_encode($frontPage);


/**
 *
 */

$dataStore          = new CBDataStore(CBPageTypeID);
$dataStoreDirectory = $dataStore->directory();

// TODO: Hack there should be a time when this directory is created.

if (!file_exists($dataStoreDirectory))
{
    $dataStore->makeDirectory();
}

file_put_contents("{$dataStoreDirectory}/front-page.json", $frontPageJSON, LOCK_EX);


/**
 * Send the response
 */

$response->message = 'This page was successfully set as the front page.';
$response->wasSuccessful = true;

$response->send();
