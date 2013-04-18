<?php

if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    include Colby::findHandler('handle-authorization-failed.php');

    exit;
}

$archive = ColbyArchive::open($_GET['archive-id']);

$documentGroupId = $archive->valueForKey('documentGroupId');
$documentTypeId = $archive->valueForKey('documentTypeId');

include Colby::findFileForDocumentType('view.php', $documentGroupId, $documentTypeId);
