<?php

include_once COLBY_DIRECTORY . '/classes/ColbyDocument.php';
include_once COLBY_DIRECTORY . '/snippets/shared/documents-administration.php';

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
$queryFieldName = $_POST['query-field-name'];
$queryFieldValue = $_POST['query-field-value'];
// $queryName = $_POST['query-name'];

$document = ColbyDocument::documentWithArchiveId(COLBY_DOCUMENTS_ADMINISTRATION_SHARED_ARCHIVE_ID);

$data->document = $document;

if (0 == $partIndex)
{
    // reset query for name
}

error_log("Part: {$partIndex} Field name: {$queryFieldName} Field value: {$queryFieldValue}");
//explorePart($partIndex);

$document->save();

/**
 * Send the response.
 */

$response->wasSuccessful = true;
$response->message = "Ran query for part {$partIndex}.";

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

    $archivesForPart = array();

    $partArchiveDirectories = glob(COLBY_DATA_DIRECTORY . "/{$hexPartIndex}/*/*");

    foreach ($partArchiveDirectories as $archiveDirectory)
    {
        preg_match('/([0-9a-f]{2})\/([0-9a-f]{2})\/([0-9a-f]{36})/', $archiveDirectory, $matches);

        $archiveId = $matches[1] . $matches[2] . $matches[3];

        $theArchive = ColbyArchive::open($archiveId);

        $archiveData = new stdClass();

        $archiveData->documentGroupId = $theArchive->valueForKey('documentGroupId');

        $archivesForPart[$archiveId] = $archiveData;
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
    LOWER(HEX(`archiveId`)) AS `archiveId`,
    LOWER(HEX(`groupId`)) AS `documentGroupId`
FROM
    `ColbyPages`
WHERE
    `archiveId` LIKE CONCAT('\\\\', UNHEX('{$hexPartIndex}'), '%')
EOT;

    $result = Colby::query($sql);

    $documentsForPart = array();

    while ($row = $result->fetch_object())
    {
        $documentData = new stdClass();

        $documentData->documentGroupId = $row->documentGroupId;

        $documentsForPart[$row->archiveId] = $documentData;
    }

    $result->free();

    /**
     *
     */

    $archiveIdsForPart = array_keys($archivesForPart);
    $documentArchiveIdsForPart = array_keys($documentsForPart);

    $archive = $data->document->archive();

    /**
     *
     */

    $strayArchives = $archive->valueForKey('strayArchives');

    $strayArchiveIdsForPart = array_diff($archiveIdsForPart, $documentArchiveIdsForPart);

    foreach ($strayArchiveIdsForPart as $archiveId)
    {
        $strayArchives->offsetSet($archiveId, $archivesForPart[$archiveId]);
    }

    /**
     *
     */

    $strayDocuments = $archive->valueForKey('strayDocuments');

    $strayDocumentArchiveIdsForPart = array_diff($documentArchiveIdsForPart, $archiveIdsForPart);

    foreach ($strayDocumentArchiveIdsForPart as $archiveId)
    {
        $strayDocuments->offsetSet($archiveId, $documentsForPart[$archiveId]);
    }
}
