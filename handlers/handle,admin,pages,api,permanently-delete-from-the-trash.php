<?php

if (!ColbyUser::current()->isOneOfThe('Administrators')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed-ajax.php';
}

$response               = new CBAjaxResponse();
$dataStoreID            = $_POST['dataStoreID'];
$response->dataStoreID  = $dataStoreID;
$frontPageFilename      = CBDataStore::filepath(['ID' => CBPageTypeID, 'filename' => 'front-page.json']);

if (file_exists($frontPageFilename)) {
    $frontPage = json_decode(file_get_contents($frontPageFilename));

    if ($dataStoreID == $frontPage->dataStoreID) {
        $response->message = 'This page is currently the front page and can\'t be deleted.';
        goto done;
    }
}

Colby::query('START TRANSACTION');

CBPages::deleteRowWithDataStoreIDFromTheTrash($dataStoreID);

/**
 * In most cases the data store directory can be assumed to exists but there
 * are rare scenarios where it won't and deleting a page shouldn't fail in those
 * scenarios.
 */

if (is_dir(CBDataStore::directoryForID($dataStoreID))) {
    CBDataStore::deleteForID(['ID' =>$dataStoreID]);
}

Colby::query('COMMIT');

/**
 * Send the response
 */

$response->wasSuccessful = true;

done:

$response->send();
