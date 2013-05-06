<?php

if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    include Colby::findHandler('handle-authorization-failed.php');

    exit;
}

/**
 * Simulate what ColbyRequest would do if it were actually handling a request.
 */

$archive = ColbyArchive::open($_GET['archive-id']);

ColbyRequest::$archive = $archive;

$documentGroupId = $archive->valueForKey('documentGroupId');
$documentTypeId = $archive->valueForKey('documentTypeId');

include Colby::findFileForDocumentType('view.php', $documentGroupId, $documentTypeId);
