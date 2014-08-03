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

$dataStore          = new CBDataStore(CBPageTypeID);
$frontPageFilename  = $dataStore->directory() . '/front-page.json';

if (file_exists($frontPageFilename))
{
    $frontPage = json_decode(file_get_contents($frontPageFilename));

    if ($dataStoreID == $frontPage->dataStoreID)
    {
        $response->message = 'This page is currently the front page and can\'t be deleted.';

        goto done;
    }
}


/**
 *
 */

Colby::mysqli()->autocommit(false);

CBPages::deleteRowWithDataStoreIDFromTheTrash($dataStoreID);

$dataStore = new CBDataStore($dataStoreID);
$dataStore->delete();

Colby::mysqli()->commit();
Colby::mysqli()->autocommit(true);


/**
 * Send the response
 */

$response->wasSuccessful = true;

done:

$response->send();
