<?php

Colby::useAjax();
Colby::useBlog();

ColbyAjax::requireVerifiedUser();

include_once(COLBY_SITE_DIRECTORY . '/colby/classes/ColbyGenericBlogPost.php');

ColbyAjax::begin();

$response = new stdClass();
$response->wasSuccessful = false;
$response->message = 'incomplete';

$archive = ColbyArchive::open($_POST['archive-id']);

if (!$archive->attributes()->created)
{
    $archive->setRootObject(new ColbyGenericBlogPost());
}

$rootObject = $archive->rootObject();

$rootObject->title = $_POST['title'];
$rootObject->titleHTML = ColbyConvert::textToHTML($rootObject->title);
$rootObject->content = $_POST['content'];
$rootObject->contentHTML = ColbyConvert::textToFormattedContent($rootObject->content);

$archive->save();

ColbyBlog::update($_POST['archive-id'], 'foo' . rand(), $archive->attributes()->created);

$response->wasSuccessful = true;
// just send a response back that indications the communication worked
$response->message = "Title: {$_POST['title']}";

echo json_encode($response);

ColbyAjax::end();
