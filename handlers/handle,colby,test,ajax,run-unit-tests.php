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
    $fileId = sha1(microtime() . rand());

    ColbyArchiver::createFileWithFileId($fileId);

    $object1 = new stdClass();
    $object1->message = 'test';

    ColbyArchiver::archiveRootObjectWithFileId($object1, $fileId);

    $object2 = ColbyArchiver::unarchiveRootObjectWithFileId($fileId);

    if ($object2->message != 'test')
    {
        throw new RuntimeException(__FUNCTION__ . ' failed.');
    }
}

function ColbyArchiverInvalidFileIdTest()
{
    $fileId = 'abadf00d';

    try
    {
        ColbyArchiver::createFilewithFileId($fileId);
    }
    catch (InvalidArgumentException $e)
    {
        return;
    }

    throw new RuntimeException(__FUNCTION__ . ' failed.');
}
