<?php

// This ajax does not require a verified user, so it must either run only when appropriate or be non-destructive.

Colby::useAjax();

ColbyAjax::begin();

$response = new stdClass();
$response->wasSuccessful = false;
$response->message = 'incomplete';

//
// Test ColbyArchiver.php
//

ColbyArchiverBasicTest();
ColbyArchiverInvalidFileIdTest();

//
// Unit Tests Complete
//

$response->wasSuccessful = true;
$response->message = 'Unit tests ran successfully.';

echo json_encode($response);

ColbyAjax::end();

function ColbyArchiverBasicTest()
{
    $archiveId = sha1(microtime() . rand());

    $object0 = new stdClass();

    $archive = ColbyArchive::open($archiveId);

    $archive->rootObject()->message = 'test';

    $archive->save();

    $hash = $archive->attributes()->hash;

    $archive = null;

    $archive = ColbyArchive::open($archiveId, $hash);

    if (false === $archive)
    {
        throw new RuntimeException(__FUNCTION__ . ' failed: unable to re-open archive');
    }

    if ($archive->rootObject()->message != 'test')
    {
        throw new RuntimeException(__FUNCTION__ . ' failed: data mismatch');
    }

    ColbyArchive::delete($archiveId);
}

function ColbyArchiverInvalidFileIdTest()
{
    $archiveId = 'abadf00d';

    try
    {
        $archvie = ColbyArchive::open($archiveId);
    }
    catch (InvalidArgumentException $e)
    {
        return;
    }

    throw new RuntimeException(__FUNCTION__ . ' failed');
}
