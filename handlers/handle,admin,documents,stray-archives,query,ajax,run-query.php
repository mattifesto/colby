<?php

include_once COLBY_SYSTEM_DIRECTORY . '/classes/ColbyDocument.php';
include_once COLBY_SYSTEM_DIRECTORY . '/snippets/shared/documents-administration.php';

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
    if (!$archive->valueForKey('reports'))
    {
        $reportsBuilder = ColbyNestedDictionaryBuilder::builderWithTitle('Stray archive query reports.');

        $archive->setObjectValueForKey($reportsBuilder->nestedDictionary(), 'reports');
    }
    else
    {
        $reportsBuilder = ColbyNestedDictionaryBuilder::builderWithNestedDictionary($archive->valueForKey('reports'));
    }

    $reportsBuilder->addValue($reportId, 'name', $reportName);
    $reportsBuilder->addValue($reportId, 'queryFieldName', $queryFieldName);
    $reportsBuilder->addValue($reportId, 'queryFieldValue', $queryFieldValue);

    $data->resultArchiveIds = new ArrayObject();

    $reportsBuilder->addValue($reportId, 'resultArchiveIds', $data->resultArchiveIds);
}
else
{
    $data->resultArchiveIds = $archive->valueForKey('reports')->items->{$reportId}->resultArchiveIds;
}

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

    $strayArchiveIds = $data->document->archive()->valueForKey('strayArchiveIds');

    foreach ($strayArchiveIds as $strayArchiveId)
    {
        if (substr($strayArchiveId, 0, 2) != $hexPartIndex)
        {
            continue;
        }

        $strayArchive = ColbyArchive::open($strayArchiveId);

        if ($strayArchive->valueForKey($queryFieldName) == $queryFieldValue)
        {
            $data->resultArchiveIds->append($strayArchiveId);
        }
    }
}
