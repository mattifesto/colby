<?php

Colby::useImage();

$response = ColbyOutputManager::beginVerifiedUserAjaxResponse();

$archive = ColbyArchive::open($_POST['archive-id']);
$data = $archive->data();
$pageModel = ColbyPageModel::modelWithData($data);

if (!$pageModel->viewId())
{
    $pageModel->setViewId($_POST['view-id']);
}

$pageModel->setTitle($_POST['title']);

$pageModel->setSubtitle($_POST['subtitle']);

$pageModel->setPageStubData($_POST['preferred-page-stub'],
                            $_POST['stub-is-locked'],
                            $_POST['custom-page-stub-text']);

$pageModel->setPublicationData($_POST['is-published'],
                               $_POST['published-by'],
                               $_POST['publication-date']);

$data->content = $_POST['content'];
$data->contentHTML = ColbyConvert::textToFormattedContent($data->content);

if (isset($_FILES['image']))
{
    $absoluteMasterImageFilename = ColbyImage::importUploadedImage('image', $archive->path());

    // Create an images sized for viewing in the post.

    $absoluteResizedImageFilename = ColbyImage::createImageByFitting($absoluteMasterImageFilename,
                                                                     array(400, PHP_INT_MAX));

    $data->imageFilename = basename($absoluteResizedImageFilename);

    // Create a thumbnail image.

    $absoluteThumbnailImageFilename = ColbyImage::createImageByFilling($absoluteMasterImageFilename,
                                                                       array(400, 400));

    // TODO: Either support png or force jpg.

    rename($absoluteThumbnailImageFilename, $archive->path('thumbnail.jpg'));

    // Delete master image because we have no need for it.

    unlink($absoluteMasterImageFilename);
}

$pageModel->updateDatabase();
$archive->save();

$response->pageStub = $pageModel->pageStub();
$response->wasSuccessful = true;
//$response->message = var_export($_POST, true);
$response->message = 'Post last updated: ' . ColbyConvert::timestampToLocalUserTime(time());

$response->end();
