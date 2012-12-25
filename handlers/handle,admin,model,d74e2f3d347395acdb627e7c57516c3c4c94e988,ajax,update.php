<?php

/**
 * This is the model updater for a model with a title, subtitle, and content.
 */

$response = ColbyOutputManager::beginVerifiedUserAjaxResponse();

$archive = ColbyArchive::open($_POST['archive-id']);
$model = ColbyPageModel::modelWithArchive($archive);

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

$archive->setMarkdownValueForKey($_POST['content'], 'content');

$model->setContentSearchText($archive->valueForKey('content'));

$model->updateDatabase();
$archive->save();

$response->pageStub = $model->pageStub();
$response->wasSuccessful = true;
//$response->message = var_export($_POST, true);
$response->message = 'Post last updated: ' . ColbyConvert::timestampToLocalUserTime(time());

$response->end();
