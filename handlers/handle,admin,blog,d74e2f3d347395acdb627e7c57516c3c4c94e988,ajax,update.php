<?php

Colby::useAjax();
Colby::useBlog();

ColbyAjax::requireVerifiedUser();

ColbyAjax::begin();

$response = new stdClass();
$response->wasSuccessful = false;
$response->message = 'incomplete';

$archive = ColbyArchive::open($_POST['archive-id']);

$data = $archive->rootObject();

if (!$archive->attributes()->created)
{
    $data->type = 'd74e2f3d347395acdb627e7c57516c3c4c94e988';
}

$data->stub = ColbyConvert::textToStub($_POST['title']);
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

$response->stub = $data->stub;
$response->wasSuccessful = true;
$response->message = 'Post last updated: ' . ColbyConvert::timestampToLocalUserTime(time());

echo json_encode($response);

ColbyAjax::end();
