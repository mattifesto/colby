<?php

Colby::useAjax();
Colby::useBlog();

ColbyAjax::requireVerifiedUser();

ColbyAjax::begin();

$response = new stdClass();
$response->wasSuccessful = false;
$response->message = 'incomplete';

$archive = ColbyArchive::open($_POST['archive-id']);

$rootObject = $archive->rootObject();

if (!$archive->attributes()->created)
{
    $rootObject->type = 'd74e2f3d347395acdb627e7c57516c3c4c94e988';
}

$rootObject->content = $_POST['content'];
$rootObject->contentHTML = ColbyConvert::textToFormattedContent($rootObject->content);
$rootObject->published = NULL;
$rootObject->stub = ColbyConvert::textToStub($_POST['title']);
$rootObject->title = $_POST['title'];
$rootObject->titleHTML = ColbyConvert::textToHTML($rootObject->title);

$archive->save();

ColbyBlog::updateDatabaseWithPostArchive($archive);

$response->stub = $rootObject->stub;
$response->wasSuccessful = true;
$response->message = 'Post Last Updated: ' . ColbyConvert::timestampToLocalUserTime(time());

echo json_encode($response);

ColbyAjax::end();
