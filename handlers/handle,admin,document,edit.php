<?php

if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}


$documentGroupId = $_GET['document-group-id'];
$documentTypeId = $_GET['document-type-id'];

if (isset($_GET['archive-id']))
{
    include Colby::findFileForDocumentType('edit.php', $documentGroupId, $documentTypeId);
}
else
{
    $archiveId = sha1(microtime() . rand());

    header("Location: /admin/document/edit/" .
           "?archive-id={$archiveId}" .
           "&document-group-id={$documentGroupId}" .
           "&document-type-id={$documentTypeId}");
}
