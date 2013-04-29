<?php

define('TEST_DOCUMENT_GROUP_ID', '427998e34c31e5410b730cd9993d5cc06bff6132');
define('TEST_DOCUMENT_TYPE_ID',  'd74e2f3d347395acdb627e7c57516c3c4c94e988');
define('TEST_ARCHIVE_ID',        '2626aac5b3aa6ba8ae35e38067e11dc307c537c2');
define('TEST_URI', 'test/the-test-document');

$response = new ColbyOutputManager('ajax-response');

$response->begin();

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    $response->message = 'You are not authorized to use this feature.';

    goto done;
}

//
// Test ColbyArchive class
//

ColbyArchiveCreateAndDeleteTest();
ColbyArchiveInvalidFileIdTest();

/*
 * ColbyMarkaroundParser
 */
include COLBY_SITE_DIRECTORY . '/colby/tests/TestColbyMarkaroundParser.php';

//
// Unit Tests Complete
//

$response->wasSuccessful = true;
$response->message = 'The unit tests ran successfully.';

done:

$response->end();

function ColbyArchiveInvalidFileIdTest()
{
    $archiveId = 'abadf00d';

    try
    {
        $archive = ColbyArchive::open($archiveId);
    }
    catch (InvalidArgumentException $e)
    {
        return;
    }

    throw new RuntimeException(__FUNCTION__ . ' failed');
}

function ColbyArchiveCreateAndDeleteTest()
{
    // Ensure there isn't an already left over from a previous failed test

    if (is_file(ColbyArchive::absoluteDataDirectoryForArchiveId(TEST_ARCHIVE_ID) . '/archive.data'))
    {
        ColbyArchive::deleteArchiveWithArchiveId(TEST_ARCHIVE_ID);

        if (is_file(ColbyArchive::absoluteDataDirectoryForArchiveId(TEST_ARCHIVE_ID) . '/archive.data'))
        {
            throw new RuntimeException(__FUNCTION__ . ' failed: Unable to clean up test evironment.');
        }
    }

    // Test creating and saving an archive

    $archive = ColbyArchive::open(TEST_ARCHIVE_ID);

    if ($archive->created() !== null)
    {
        throw new RuntimeException(__FUNCTION__ . 'failed: A new unsaved archive should return `null` from the `created` method.');
    }

    $title = 'The Title';

    $archive->setStringValueForKey($title, 'title');

    $archive->save();

    $archive = null;

    // Test opening an archive

    $archive = ColbyArchive::open(TEST_ARCHIVE_ID);

    if ($archive->archiveId() !== TEST_ARCHIVE_ID)
    {
        throw new RuntimeException(__FUNCTION__ . ' failed: The archive id after opening doesn\'t match the saved archive id.');
    }

    $archive = null;

    // Test deleting an archive

    ColbyArchive::deleteArchiveWithArchiveId(TEST_ARCHIVE_ID);

    if (is_file(ColbyArchive::absoluteDataDirectoryForArchiveId(TEST_ARCHIVE_ID) . '/archive.data'))
    {
        throw new RuntimeException(__FUNCTION__ . ' failed: The archive is still exists after deletion.');
    }
}

class ColbyUnitTests
{
    public static function VerifyActualStringIsExpected($actual, $expected)
    {
        if ($actual != $expected)
        {
            $expected2 = ColbyConvert::textToTextWithVisibleWhitespace($expected);
            $actual2 = ColbyConvert::textToTextWithVisibleWhitespace($actual);

            throw new RuntimeException("expected: \"{$expected2}\", actual: \"{$actual2}\"");
        }
    }
}
