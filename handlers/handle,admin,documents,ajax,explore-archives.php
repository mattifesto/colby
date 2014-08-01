<?php

include_once COLBY_SYSTEM_DIRECTORY . '/snippets/shared/documents-administration.php';

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed-ajax.php';
}

$response = new CBAjaxResponse();

global $data;

$data = new stdClass();

$partIndex = (int)$_POST['part-index'];

if (0 == $partIndex)
{
    ColbyDocument::deleteDocumentWithArchiveId(COLBY_DOCUMENTS_ADMINISTRATION_SHARED_ARCHIVE_ID);
}

$document = ColbyDocument::documentWithArchiveId(COLBY_DOCUMENTS_ADMINISTRATION_SHARED_ARCHIVE_ID);

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

    $archive->setObjectValueForKey(new ArrayObject(), 'strayArchiveIds');
    $archive->setObjectValueForKey(new ArrayObject(), 'strayDocumentArchiveIds');
}

explorePart($partIndex);

$document->save();

/**
 * Send the response.
 */

$response->wasSuccessful = true;
$response->message = 'Part explored.';

$response->send();

/* ---------------------------------------------------------------- */

/**
 * @return void
 */
function explorePart($partIndex)
{
    global $data;

    $hexPartIndex = sprintf('%02x', $partIndex);

    $archiveIdsForPart = array();

    $partArchiveDirectories = glob(COLBY_DATA_DIRECTORY . "/{$hexPartIndex}/*/*");

    foreach ($partArchiveDirectories as $archiveDirectory)
    {
        if (!preg_match('/([0-9a-f]{2})\/([0-9a-f]{2})\/([0-9a-f]{36})/', $archiveDirectory, $matches))
        {
            throw new RuntimeException("The archive directory `{$archiveDirectory}` is malformed. Investigate and remove the directory manually to continue.");
        }

        $archiveId = $matches[1] . $matches[2] . $matches[3];

        $archiveIdsForPart[] = $archiveId;
    }

    /**
     * Get archiveIds for all of the documents in the part.
     *
     * CONCAT has three parts:
     *
     *  '\\\\'
     *      This will evaluate to '\\' in the SQL which will then evaluate to
     *      a single backslash which will escape the character that follows it
     *      which will be necessary if that character happens to be '%'.
     *
     *  UNHEX('{$hexPartIndex}')
     *      Since `hexPartIndex` is two hex characters this will evaluate to
     *      a single "binary character set" character.
     *
     *  '%'
     *      This percent is the wildcard character to be used in the context
     *      of the 'LIKE' keyword.
     */

    $sql = <<<EOT
SELECT
    LOWER(HEX(`archiveId`)) AS `archiveId`
FROM
    `ColbyPages`
WHERE
    `archiveId` LIKE CONCAT('\\\\', UNHEX('{$hexPartIndex}'), '%')
EOT;

    $result = Colby::query($sql);

    $documentArchiveIdsForPart = array();

    while ($row = $result->fetch_object())
    {
        $documentArchiveIdsForPart[] = $row->archiveId;
    }

    $result->free();

    /**
     *
     */

    $archive = $data->document->archive();

    $strayArchiveIds = $archive->valueForKey('strayArchiveIds');

    $strayArchiveIdsForPart = array_diff($archiveIdsForPart, $documentArchiveIdsForPart);

    foreach ($strayArchiveIdsForPart as $strayArchiveId)
    {
        $strayArchiveIds->append($strayArchiveId);
    }

    /**
     *
     */

    $strayDocumentArchiveIds = $archive->valueForKey('strayDocumentArchiveIds');

    $strayDocumentArchiveIdsForPart = array_diff($documentArchiveIdsForPart, $archiveIdsForPart);

    foreach ($strayDocumentArchiveIdsForPart as $strayDocumentArchiveId)
    {
        $strayDocumentArchiveIds->append($strayDocumentArchiveId);
    }
}
