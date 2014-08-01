<?php

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed-ajax.php';
}

include_once COLBY_SYSTEM_DIRECTORY . '/snippets/shared/documents-administration.php';

$response = new CBAjaxResponse();

global $data;

$data = new stdClass();

$reportId = $_POST['report-id'];

$document = ColbyDocument::documentWithArchiveId(COLBY_DOCUMENTS_ADMINISTRATION_SHARED_ARCHIVE_ID);

$data->document = $document;

$allArchivesWereDeleted = deleteArchivesForReportId($reportId);

if (!$allArchivesWereDeleted)
{
    $response->hasMoreArchivesToDelete = true;
}

$document->save();

/**
 * Send the response.
 */

$response->wasSuccessful = true;
$response->message = "Deleted {$data->countOfArchivesDeleted} archives.";

$response->send();

/* ---------------------------------------------------------------- */

/**
 * @return bool
 *  Returns true if all of the archives in the report were deleted; otherwise
 *  false.
 */
function deleteArchivesForReportId($reportId)
{
    global $data;

    $strayArchiveIds = $data->document->archive()->valueForKey('reports')->items->{$reportId}->resultArchiveIds;

    $timeout = time() + 10;

    $data->countOfArchivesDeleted = 0;

    foreach ($strayArchiveIds as $index => $strayArchiveId)
    {
        ColbyArchive::deleteArchiveWithArchiveId($strayArchiveId);

        $data->countOfArchivesDeleted++;

        if (time() > $timeout)
        {
            return false;
        }
    }

    return true;
}
