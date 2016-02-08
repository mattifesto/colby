<?php

if (!ColbyUser::current()->isOneOfThe('Administrators')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed-ajax.php';
}

$response               = new CBAjaxResponse();
$dataStoreID            = $_POST['dataStoreID'];
$response->dataStoreID  = $dataStoreID;

Colby::query('START TRANSACTION');

CBPages::deleteRowWithDataStoreIDFromTheTrash($dataStoreID);

/**
 * @NOTE
 * We used to delete the data store here, but the problem is that on some older
 * websites the data stores would contain images that we should not delete. Now
 * nothing is deleted but in the future it's possible that it may be wise to
 * delete some specific files.
 */

Colby::query('COMMIT');

/**
 * Send the response
 */

$response->wasSuccessful = true;
$response->send();
