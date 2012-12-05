<?php

$response = ColbyOutputManager::beginVerifiedUserAjaxResponse();

$archive = ColbyArchive::open($_POST['archive-id']);

$page = $archive->rootObject();

// TODO: better place for model id?

$modelId = 'd74e2f3d347395acdb627e7c57516c3c4c94e988';
$groupId = $_POST['group-id'];
$groupStub = $_POST['group-stub'];

if (!$archive->attributes()->created)
{
    $page = new ColbyPage($modelId, $groupId, $groupStub);
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

$page->updateDatabaseWithArchiveId($_POST['archive-id']);

$archive->save();

$response->pageStub = $page->pageStub;
$response->wasSuccessful = true;
//$response->message = var_export($_POST, true);
$response->message = 'Post last updated: ' . ColbyConvert::timestampToLocalUserTime(time());

$response->end();
