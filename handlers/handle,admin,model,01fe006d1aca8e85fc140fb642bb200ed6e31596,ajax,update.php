<?php

Colby::useImage();

$response = ColbyOutputManager::beginVerifiedUserAjaxResponse();

$archiveId = $_POST['archive-id'];
$archive = ColbyArchive::open($archiveId);

if ($archive->attributes()->created)
{
    $page = $archive->rootObject();
}
else
{
    $viewId = $_POST['view-id'];

    $page = ColbyPage::pageWithViewId($viewId);

    $archive->setRootObject($page);
}

$page->setTitle($_POST['title']);

$page->setSubtitle($_POST['subtitle']);

$page->setPageStubData($_POST['preferred-page-stub'],
                       $_POST['stub-is-locked'],
                       $_POST['custom-page-stub-text']);

$page->setPublicationData($_POST['is-published'],
                          $_POST['published-by'],
                          $_POST['publication-date']);

$page->content = $_POST['content'];
$page->contentHTML = ColbyConvert::textToFormattedContent($page->content);

if (isset($_FILES['image']))
{
    $absoluteArchiveDirectory = COLBY_DATA_DIRECTORY . "/{$archiveId}";

    $absoluteMasterImageFilename = ColbyImage::importUploadedImage('image', $absoluteArchiveDirectory);

    // Create an images sized for viewing in the post.

    $absoluteResizedImageFilename = ColbyImage::createImageByFitting($absoluteMasterImageFilename,
                                                                     array(400, PHP_INT_MAX));

    $page->imageFilename = basename($absoluteResizedImageFilename);

    // Create a thumbnail image.

    $absoluteThumbnailImageFilename = ColbyImage::createImageByFitting($absoluteMasterImageFilename,
                                                                       array(400, 400));

    // TODO: Either support png or force jpg.

    rename($absoluteThumbnailImageFilename, "{$absoluteArchiveDirectory}/thumbnail.jpg");

    // Delete master image because we have no need for it.

    unlink($absoluteMasterImageFilename);
}

$page->updateDatabaseWithArchiveId($archiveId);

$archive->save();

$response->pageStub = $page->pageStub;
$response->wasSuccessful = true;
//$response->message = var_export($_POST, true);
$response->message = 'Post last updated: ' . ColbyConvert::timestampToLocalUserTime(time());

$response->end();
