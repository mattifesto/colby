<?php

$response = ColbyOutputManager::beginVerifiedUserAjaxResponse();

Colby::useBlog();

$archive = ColbyArchive::open($_POST['archive-id']);

$data = $archive->rootObject();

// TODO: better place for model id?

$modelId = 'd74e2f3d347395acdb627e7c57516c3c4c94e988';
$groupId = $_POST['group-id'];
$groupStub = $_POST['group-stub'];

// TODO: in the future set page to the root object

$page = new ColbyPage($modelId, $groupId, $groupStub);

if (!$archive->attributes()->created)
{
    $page = new ColbyPage($modelId, $groupId, $groupStub);
    $data->type = $modelId;
}

$page->setTitle($_POST['title']);

$page->setSubtitle($_POST['subtitle']);

$page->setPageStubData($_POST['preferred-page-stub'],
                       $_POST['stub-is-locked'],
                       $_POST['custom-page-stub-text']);

$page->setPublicationData($_POST['is-published'],
                          $_POST['published-by'],
                          $_POST['publication-date']);

$data->stub = $_POST['preferred-page-stub'];
$data->stubIsLocked = $_POST['stub-is-locked'];
$data->title = $_POST['title'];
$data->titleHTML = ColbyConvert::textToHTML($data->title);
$data->subtitle = $_POST['subtitle'];
$data->subtitleHTML = ColbyConvert::textToHTML($data->subtitle);
$data->content = $_POST['content'];
$data->contentHTML = ColbyConvert::textToFormattedContent($data->content);

// TODO: do we want to do some data validation on these dates?

$wasPublished = isset($data->published);

$data->published = empty($_POST['published']) ? null : $_POST['published'];
$data->publicationDate = empty($_POST['publication-date']) ? null : $_POST['publication-date'];

if (!isset($data->publishedBy))
{
    $data->publishedBy = null;
}

// If this is the first time the post has been published, assign published by
// TODO: maybe this should be controlled more by the UI side?

if (   !$wasPublished
    && $data->published
    && !$data->publishedBy)
{
    $data->publishedBy = ColbyUser::currentUserId();
}

$archive->save();

ColbyBlog::updateDatabaseWithPostArchive($archive);
$page->updateDatabaseWithArchiveId($_POST['archive-id']);

$response->pageStub = $page->pageStub;
$response->wasSuccessful = true;
//$response->message = var_export($_POST, true);
$response->message = 'Post last updated: ' . ColbyConvert::timestampToLocalUserTime(time());

$response->end();
