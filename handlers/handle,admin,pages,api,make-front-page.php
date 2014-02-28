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

done:

$response->end();
