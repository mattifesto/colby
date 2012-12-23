<?php

Colby::useImage();

$response = ColbyOutputManager::beginVerifiedUserAjaxResponse();

$archive = ColbyArchive::open($_POST['archive-id'], true);
$data = $archive->data();
$model = ColbyPageModel::modelWithData($data);

if (!$model->viewId())
{
    $model->setViewId($_POST['view-id']);
}

$archive->setStringForName($_POST['title'], 'title');

$model->setSubtitle($_POST['subtitle']);

$model->setPageStubData($_POST['preferred-page-stub'],
                            $_POST['stub-is-locked'],
                            $_POST['custom-page-stub-text']);

$model->setPublicationData($_POST['is-published'],
                               $_POST['published-by'],
                               $_POST['publication-date']);

$data->content = $_POST['content'];
$data->contentHTML = ColbyConvert::textToFormattedContent($data->content);

$model->setContentSearchText($data->content);

if (isset($_FILES['image']))
{
    $absoluteMasterImageFilename = ColbyImage::importUploadedImage('image', $archive->path());

    // Create an images sized for viewing in the post.

    $absoluteResizedImageFilename = ColbyImage::createImageByFitting($absoluteMasterImageFilename,
                                                                     array(500, PHP_INT_MAX));

    $data->imageFilename = basename($absoluteResizedImageFilename);

    // Create a thumbnail image.

    $absoluteThumbnailImageFilename = ColbyImage::createImageByFilling($absoluteMasterImageFilename,
                                                                       array(400, 400));

    // TODO: Either support png or force jpg.

    rename($absoluteThumbnailImageFilename, $archive->path('thumbnail.jpg'));

    // Delete master image because we have no need for it.

    unlink($absoluteMasterImageFilename);
}

$model->updateDatabase();
$archive->save();

$response->pageStub = $model->pageStub();
$response->wasSuccessful = true;
//$response->message = var_export($_POST, true);
$response->message = 'Post last updated: ' . ColbyConvert::timestampToLocalUserTime(time());

$response->end();
