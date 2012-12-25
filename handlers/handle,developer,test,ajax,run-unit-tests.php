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
// Test ColbyPageModel class
//

ColbyPageModelCreateAndDeleteTest();

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

    $archive->data()->message = 'test';

    $archive->save();

    $hash = $archive->hash();

    $archive = null;

    $archive = ColbyArchive::open($archiveId, $hash);

    if (false === $archive)
    {
        throw new RuntimeException(__FUNCTION__ . ' failed: unable to re-open archive');
    }

    if ($archive->data()->message != 'test')
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

function ColbyPageModelCreateAndDeleteTest()
{
    // make sure there isn't a document already left over from a previous failed attempt

    $archive = ColbyPageModel::archiveForStub('test/the-test-post');

    if ($archive)
    {
        ColbyPageModel::delete($archive->archiveId());

        if (ColbyPageModel::archiveForStub('test/the-test-post'))
        {
            throw new RuntimeException(__FUNCTION__ . ' failed: Unable to clean up test evironment.');
        }

        $archive = null;
    }

    // begin tests

    $archiveId = sha1(microtime() . rand());

    $archive = ColbyArchive::open($archiveId);

    if ($archive->created())
    {
        throw new RuntimeException(__FUNCTION__ . 'failed: The archive already exists.');
    }

    $title = 'Test post title';
    $subtitle = 'Test post subtitle.';
    $content = 'This is the content for a test post.';

    $model = ColbyPageModel::modelWithArchive($archive);
    $model->setModelId(SIMPLE_CONTENT_DOCUMENT_MODEL_ID);
    $model->setGroupId(TEST_GROUP_ID);
    $model->setGroupStub('test');

    $archive->setStringValueForKey($title, 'title');
    $archive->setStringValueForKey($subtitle, 'subtitle');
    $model->setPageStubData('the-test-post', false);

    $archive->setMarkdownValueForKey($content, 'content');

    $model->updateDatabase();
    $archive->save();

    $archive = null;
    $model = null;

    $archive = ColbyPageModel::archiveForStub('test/the-test-post');

    if (!$archive)
    {
        throw new RuntimeException(__FUNCTION__ . ' failed: Unable to retreive the archive using the stub.');
    }

    if ($archive->archiveId() != $archiveId)
    {
        throw new RuntimeException(__FUNCTION__ . ' failed: Archive id after loading doesn\'t match the save archive id.');
    }

    $archive = null;

    ColbyPageModel::delete($archiveId);

    $archive = ColbyPageModel::archiveForStub('test/the-test-post');

    if ($archive)
    {
        throw new RuntimeException(__FUNCTION__ . ' failed: The archive is still retreivable after deleting.');
    }
}
