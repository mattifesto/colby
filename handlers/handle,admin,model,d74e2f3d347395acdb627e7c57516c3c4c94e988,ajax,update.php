<?php

/**
 * This is the model updater for a model with a title, subtitle, and content.
 */

$response = ColbyOutputManager::beginVerifiedUserAjaxResponse();

$archive = ColbyArchive::open($_POST['archive-id']);
$data = $archive->data();
$model = ColbyPageModel::modelWithData($data);

if (!$model->viewId())
{
    $model->setViewId($_POST['view-id']);
}

$archive->setStringValueForKey($_POST['title'], 'title');
$archive->setStringValueForKey($_POST['subtitle'], 'subtitle');

$model->setPageStubData($_POST['preferred-page-stub'],
                            $_POST['stub-is-locked'],
                            $_POST['custom-page-stub-text']);

$model->setPublicationData($_POST['is-published'],
                               $_POST['published-by'],
                               $_POST['publication-date']);

$data->content = $_POST['content'];
$data->contentHTML = ColbyConvert::textToFormattedContent($data->content);

$model->setContentSearchText($data->content);

$model->updateDatabase();
$archive->save();

$response->pageStub = $model->pageStub();
$response->wasSuccessful = true;
//$response->message = var_export($_POST, true);
$response->message = 'Post last updated: ' . ColbyConvert::timestampToLocalUserTime(time());

$response->end();
