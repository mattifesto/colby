<?php

if (!ColbyUser::current()->isOneOfThe('Administrators')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed-ajax.php';
}

$response = new CBAjaxResponse();
$ID = $_POST['dataStoreID'];
$response->dataStoreID = $ID;
$IDAsSQL = CBHex160::toSQL($ID);
$count = CBDB::SQLToValue("SELECT COUNT(*) FROM `CBPagesInTheTrash` WHERE `archiveID` = {$IDAsSQL}");

if ($count === '1') {
    CBModels::deleteModelsByID([$ID]);

    /* The following line is unnecessary once all pages are stored as models. */
    CBPages::deletePagesFromTrashByID([$ID]);

    /**
     * @NOTE
     * We used to delete the data store here, but the problem is that on some older
     * websites the data stores would contain images that we should not delete. Now
     * nothing is deleted but in the future it's possible that it may be wise to
     * delete some specific files.
     */
} else {
    throw new RuntimeException('Pages must be in the trash to be deleted using this interface.');
}

/**
 * Send the response
 */

$response->wasSuccessful = true;
$response->send();
