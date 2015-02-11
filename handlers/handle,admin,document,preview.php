<?php

if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
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
        `className`,
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

if (!$row->className && CBPageTypeID == $row->typeID) {
    $row->className = 'CBViewPage';
}

if ($row->className) {
    $className  = $row->className;
    $page       = $className::initWithID($dataStoreID);
    $page->renderHTML();
} else {
    $archive = ColbyArchive::open($_GET['archive-id']);

    ColbyRequest::$archive = $archive;

    $documentGroupId = $archive->valueForKey('documentGroupId');
    $documentTypeId = $archive->valueForKey('documentTypeId');

    include Colby::findFileForDocumentType('view.php', $documentGroupId, $documentTypeId);
}
