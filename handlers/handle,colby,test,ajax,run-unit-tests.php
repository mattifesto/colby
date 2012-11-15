<?php

Colby::useBlog();

$response = ColbyOutputManager::beginVerifiedUserAjaxResponse();

//
// Test ColbyArchiver.php
//

ColbyArchiverBasicTest();
ColbyArchiverInvalidFileIdTest();

//
// Test Blog
//

ColbyBlogPostCreateAndDeleteTest();

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
        $archvie = ColbyArchive::open($archiveId);
    }
    catch (InvalidArgumentException $e)
    {
        return;
    }

    throw new RuntimeException(__FUNCTION__ . ' failed');
}

function ColbyBlogPostCreateAndDeleteTest()
{
    $archiveId = sha1('ColbyCreateAndDeleteBlogPostTests' . rand());

    $archive = ColbyArchive::open($archiveId);

    $rootObject = $archive->rootObject();

    if ($archive->attributes()->created)
    {
        throw new RuntimeException(__FUNCTION__ . 'failed: The archive already exists.');
    }

    $title = 'Test post title';
    $subtitle = 'Test post subtitle.';
    $content = 'This is the content for a test post.';

    $rootObject->type = 'd74e2f3d347395acdb627e7c57516c3c4c94e988';
    $rootObject->content = $content;
    $rootObject->contentHTML = ColbyConvert::textToFormattedContent($rootObject->content);
    $rootObject->published = null;
    $rootObject->publishedBy = null;
    $rootObject->stub = ColbyConvert::textToStub($title);
    $rootObject->title = $title;
    $rootObject->titleHTML = ColbyConvert::textToHTML($rootObject->title);
    $rootObject->subtitle = $subtitle;
    $rootObject->subtitleHTML = ColbyConvert::textToHTML($rootObject->subtitle);

    $archive->save();

    ColbyBlog::updateDatabaseWithPostArchive($archive);

    $archive = null;

    ColbyBlog::deletePost($archiveId);
}
