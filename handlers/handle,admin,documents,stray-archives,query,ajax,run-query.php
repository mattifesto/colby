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
$reportName = $_POST['report-name'];
$reportId = sha1("Stray archive report: {$reportName}");

$document = ColbyDocument::documentWithArchiveId(COLBY_DOCUMENTS_ADMINISTRATION_SHARED_ARCHIVE_ID);

$data->document = $document;

$archive = $document->archive();

if (0 == $partIndex)
{
    $reports = $archive->valueForKey('reports');

    if (!$reports)
    {
        $reports = new stdClass();

        $archive->setObjectValueForKey($reports, 'reports');
    }

    $report = new stdClass();

    $report->name = $reportName;
    $report->queryFieldName = $queryFieldName;
    $report->queryFieldValue = $queryFieldValue;
    $report->archiveIds = new ArrayObject();

    $reports->{$reportId} = $report;
}

$report = $archive->valueForKey('reports')->{$reportId};

$data->report = $report;

runQueryForPart($partIndex, $queryFieldName, $queryFieldValue);

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
function runQueryForPart($partIndex, $queryFieldName, $queryFieldValue)
{
    global $data;

    $hexPartIndex = sprintf('%02x', $partIndex);

    $archive = $data->document->archive();

    $strayArchives = $archive->valueForKey('strayArchives');

    foreach ($strayArchives as $strayArchiveId => $strayArchiveMetaData)
    {
        if (substr($strayArchiveId, 0, 2) != $hexPartIndex)
        {
            continue;
        }

        $strayArchive = ColbyArchive::open($strayArchiveId);

        if ($strayArchive->valueForKey($queryFieldName) == $queryFieldValue)
        {
            $data->report->archiveIds->append($strayArchiveId);
        }
    }
}
