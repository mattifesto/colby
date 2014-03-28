<?php

include_once CBSystemDirectory . '/classes/CBAjaxResponse.php';
include_once CBSystemDirectory . '/classes/CBDataStore.php';
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

$dataStore          = new CBDataStore(CBPageTypeID);
$frontPageFilename  = $dataStore->directory() . '/front-page.json';

if (file_exists($frontPageFilename))
{
    $frontPage = json_decode(file_get_contents($frontPageFilename));

    if ($dataStoreID == $frontPage->dataStoreID)
    {
        $response->message = 'This page is currently the front page and can\'t be moved to the trash.';

        goto done;
    }
}


/**
 *
 */

CBPages::moveRowWithDataStoreIDToTheTrash($dataStoreID);


/**
 * Send the response
 */

$response->wasSuccessful = true;

done:

$response->send();
