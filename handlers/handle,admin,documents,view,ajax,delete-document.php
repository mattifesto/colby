<?php

include_once COLBY_SYSTEM_DIRECTORY . '/classes/ColbyDocument.php';

$response = new ColbyOutputManager('ajax-response');
$response->begin();

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    $response->message = 'You do not have authorization to perform this action.';

    goto done;
}

$archiveId = $_POST['archive-id'];

ColbyDocument::deleteDocumentWithArchiveId($archiveId);

/**
 * Send the response.
 */

$response->wasSuccessful = true;
$response->message = "Deleted the document with the archive id: {$archiveId}.";

done:

$response->end();
