<?php

define('COLBY_ARCHIVES_DOCUMENT_ARCHIVE_ID', '5bda1825fe0be9524106061b910fd0b8e1dde0c2');

include_once COLBY_DIRECTORY . '/classes/ColbyDocument.php';

$response = new ColbyOutputManager('ajax-response');
$response->begin();

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    $response->message = 'You do not have authorization to perform this action.';

    goto done;
}

global $data;

$data = new stdClass();

$partIndex = (int)$_POST['part-index'];

if (0 == $partIndex)
{
    ColbyDocument::deleteDocumentWithArchiveId(COLBY_ARCHIVES_DOCUMENT_ARCHIVE_ID);
}

$document = ColbyDocument::documentWithArchiveId(COLBY_ARCHIVES_DOCUMENT_ARCHIVE_ID);

$data->document = $document;

if (0 == $partIndex)
{
    $archive = $document->archive();

    $title = 'Colby System Archives Document';
    $subtitle = 'This document contains metadata about the documents and archives used by this website.';

    $archive->setStringValueForKey($title, 'title');
    $archive->setStringValueForKey(ColbyConvert::textToHTML($title), 'titleHTML');
    $archive->setStringValueForKey($subtitle, 'subtitle');
    $archive->setStringValueForKey(ColbyConvert::textToHTML($subtitle), 'subtitleHTML');
}

explorePart($partIndex);

$document->save();

/**
 * Send the response.
 */

$response->wasSuccessful = true;
$response->message = 'Part explored.';

done:

$response->end();

/* ---------------------------------------------------------------- */

/**
 * @return void
 */
function explorePart($partIndex)
{
    global $data;

    $hexPartIndex = sprintf('%02x', $partIndex);

    $archive = $data->document->archive();

    $partObject = new stdClass();

    $partArchiveDirectories = glob(COLBY_DATA_DIRECTORY . "/{$hexPartIndex}/*/*");

    $countOfArchivesInPart = 0;

    foreach ($partArchiveDirectories as $archiveDirectory)
    {
        preg_match('/([0-9a-f]{2})\/([0-9a-f]{2})\/([0-9a-f]{36})/', $archiveDirectory, $matches);

        $archiveId = $matches[1] . $matches[2] . $matches[3];

        $archiveMetaData = new stdClass();

        $theArchive = ColbyArchive::open($archiveId);

        $archiveMetaData->titleHTML = $theArchive->valueForKey('titleHTML');
        $archiveMetaData->subtitleHTML = $theArchive->valueForKey('subtitleHTML');

        $partObject->{$archiveId} = $archiveMetaData;

        $countOfArchivesInPart++;
    }

    $partObject->countOfArchives = $countOfArchivesInPart;

    $parts = $archive->valueForKey('parts');

    if (null == $parts)
    {
        $parts = new stdClass();

        $archive->setObjectValueForKey($parts, 'parts');
    }

    $parts->{$hexPartIndex} = $partObject;
}
