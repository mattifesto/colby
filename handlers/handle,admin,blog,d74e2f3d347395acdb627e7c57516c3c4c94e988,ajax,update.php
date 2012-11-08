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

$data->title = $_POST['title'];
$data->titleHTML = ColbyConvert::textToHTML($data->title);
$data->stub = ColbyConvert::textToStub($_POST['title']);
$data->subtitle = $_POST['subtitle'];
$data->subtitleHTML = ColbyConvert::textToHTML($data->subtitle);
$data->content = $_POST['content'];
$data->contentHTML = ColbyConvert::textToFormattedContent($data->content);

// TODO: do we want to do some data validation on these dates?

$data->published = empty($_POST['published']) ? null : intval($_POST['published']);
$data->publicationDate = intval($_POST['publication-date']);

$archive->save();

ColbyBlog::updateDatabaseWithPostArchive($archive);

$response->stub = $data->stub;
$response->wasSuccessful = true;
$response->message = 'Post last updated: ' . ColbyConvert::timestampToLocalUserTime(time());

echo json_encode($response);

ColbyAjax::end();
