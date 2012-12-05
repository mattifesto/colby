<?php

define('SIMPLE_CONTENT_DOCUMENT_MODEL_ID', 'd74e2f3d347395acdb627e7c57516c3c4c94e988');
define('TEST_GROUP_ID', '427998e34c31e5410b730cd9993d5cc06bff6132');

$response = ColbyOutputManager::beginVerifiedUserAjaxResponse();

//
// Test ColbyArchiver.php
//

ColbyArchiverBasicTest();
ColbyArchiverInvalidFileIdTest();

//
// Test ColbyPage class
//

ColbyPageCreateAndDeleteTest();

//
// Unit Tests Complete
//

$response->wasSuccessful = true;
$response->message = 'The unit tests ran successfully.';

$response->end();

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
        $archive = ColbyArchive::open($archiveId);
    }
    catch (InvalidArgumentException $e)
    {
        return;
    }

    throw new RuntimeException(__FUNCTION__ . ' failed');
}

function ColbyPageCreateAndDeleteTest()
{
    // make sure there isn't a document already left over from a previous failed attempt

    $archive = ColbyPage::archiveForStub('test/the-test-post');

    if ($archive)
    {
        ColbyPage::delete($archive->archiveId());

        if (ColbyPage::archiveForStub('test/the-test-post'))
        {
            throw new RuntimeException(__FUNCTION__ . ' failed: Unable to clean up test evironment.');
        }

        $archive = null;
    }

    // begin tests

    $archiveId = sha1(microtime() . rand());

    $archive = ColbyArchive::open($archiveId);

    $rootObject = $archive->rootObject();

    if ($archive->attributes()->created)
    {
        throw new RuntimeException(__FUNCTION__ . 'failed: The archive already exists.');
    }

    $title = 'Test post title';
    $subtitle = 'Test post subtitle.';
    $content = 'This is the content for a test post.';

    $page = new ColbyPage(SIMPLE_CONTENT_DOCUMENT_MODEL_ID, TEST_GROUP_ID, 'test');

    $page->setTitle($title);
    $page->setSubtitle($subtitle);
    $page->setPageStubData('the-test-post', false);

    $page->content = $content;
    $page->contentHTML = ColbyConvert::textToFormattedContent($content);

    $page->updateDatabaseWithArchiveId($archiveId);

    $archive->setRootObject($page);
    $archive->save();

    $archive = null;
    $page = null;

    $archive = ColbyPage::archiveForStub('test/the-test-post');

    if (!$archive)
    {
        throw new RuntimeException(__FUNCTION__ . ' failed: Unable to retreive the archive using the stub.');
    }

    if ($archive->archiveId() != $archiveId)
    {
        throw new RuntimeException(__FUNCTION__ . ' failed: Archive id after loading doesn\'t match the save archive id.');
    }

    $archive = null;

    ColbyPage::delete($archiveId);

    $archive = ColbyPage::archiveForStub('test/the-test-post');

    if ($archive)
    {
        throw new RuntimeException(__FUNCTION__ . ' failed: The archive is still retreivable after deleting.');
    }
}
