<?php

if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    include Colby::findHandler('handle-authorization-failed.php');

    exit;
}


/**
 *
 */

$dataStoreID = $_GET['archive-id'];
$dataStoreIDForSQL  = ColbyConvert::textToSQL($dataStoreID);


/**
 *
 */

$sql = <<<EOT

    SELECT
        LOWER(HEX(`typeID`)) as `typeID`
    FROM
        `ColbyPages`
    WHERE
        `archiveID` = UNHEX('{$dataStoreIDForSQL}')

EOT;

$result = Colby::query($sql);

$row = $result->fetch_object();

$result->free();

if (!$row)
{
    echo 'No page exists for the provided data store ID.';

    return 1;
}

if (CBPageTypeID == $row->typeID)
{
    include CBSystemDirectory . '/handlers/handle-sectioned-page.php';
}
else
{
    $archive = ColbyArchive::open($_GET['archive-id']);

    ColbyRequest::$archive = $archive;

    $documentGroupId = $archive->valueForKey('documentGroupId');
    $documentTypeId = $archive->valueForKey('documentTypeId');

    include Colby::findFileForDocumentType('view.php', $documentGroupId, $documentTypeId);
}
