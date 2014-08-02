<?php

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed-ajax.php';
}

$response   = new CBAjaxResponse();
$archiveId  = $_POST['archive-id'];

ColbyDocument::deleteDocumentWithArchiveId($archiveId);

/**
 * Send the response.
 */

$response->wasSuccessful = true;
$response->message = "Deleted the document with the archive id: {$archiveId}.";

$response->send();
