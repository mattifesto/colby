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
$data->subhead = $_POST['subhead'];
$data->subheadHTML = ColbyConvert::textToHTML($data->subhead);
$data->content = $_POST['content'];
$data->contentHTML = ColbyConvert::textToFormattedContent($data->content);
$data->published = null;

$archive->save();

ColbyBlog::updateDatabaseWithPostArchive($archive);

$response->stub = $data->stub;
$response->wasSuccessful = true;
$response->message = 'Post last updated: ' . ColbyConvert::timestampToLocalUserTime(time());

echo json_encode($response);

ColbyAjax::end();
