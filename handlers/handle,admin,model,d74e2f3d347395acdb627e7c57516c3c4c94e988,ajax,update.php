<?php

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

$pageModel->updateDatabase();
$archive->save();

$response->pageStub = $pageModel->pageStub();
$response->wasSuccessful = true;
//$response->message = var_export($_POST, true);
$response->message = 'Post last updated: ' . ColbyConvert::timestampToLocalUserTime(time());

$response->end();
