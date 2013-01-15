<?php

/**
 * This is the model updater or a model with a title, subtitle, content, and
 * one medium sized image.
 */

Colby::useImage();

$response = ColbyOutputManager::beginVerifiedUserAjaxResponse();

$archive = ColbyArchive::archiveFromPostData();

$archive->setMarkaroundValueForKey($_POST['content'], 'content');

$archive->model->setContentSearchText($archive->valueForKey('content'));

if (isset($_FILES['image']))
{
    $absoluteMasterImageFilename = ColbyImage::importUploadedImage('image', $archive->path());

    // Create an images sized for viewing in the post.

    $absoluteResizedImageFilename = ColbyImage::createImageByFitting($absoluteMasterImageFilename,
                                                                     array(500, PHP_INT_MAX));

    $archive->setStringValueForKey(basename($absoluteResizedImageFilename), 'imageFilename', false);

    // Create a thumbnail image.

    $absoluteThumbnailImageFilename = ColbyImage::createImageByFilling($absoluteMasterImageFilename,
                                                                       array(400, 400));

    // TODO: Either support png or force jpg.

    rename($absoluteThumbnailImageFilename, $archive->path('thumbnail.jpg'));

    // Delete master image because we have no need for it.

    unlink($absoluteMasterImageFilename);
}

$archive->save();

$response->pageStub = $archive->model->pageStub();
$response->wasSuccessful = true;
//$response->message = var_export($_POST, true);
$response->message = 'Post last updated: ' . ColbyConvert::timestampToLocalUserTime(time());

$response->end();
