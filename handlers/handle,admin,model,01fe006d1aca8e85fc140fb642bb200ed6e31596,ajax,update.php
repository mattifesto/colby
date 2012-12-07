<?php

$response = ColbyOutputManager::beginVerifiedUserAjaxResponse();

$archive = ColbyArchive::open($_POST['archive-id']);

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

$page->updateDatabaseWithArchiveId($_POST['archive-id']);

$archive->save();

$response->pageStub = $page->pageStub;
$response->wasSuccessful = true;
//$response->message = var_export($_POST, true);
$fileMessage = isset($_FILES['image']) ? ' (file uploaded)' : '';
$response->message = 'Post last updated: ' . ColbyConvert::timestampToLocalUserTime(time()) . $fileMessage;

$response->end();
