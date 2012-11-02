<?php

Colby::useAjax();

ColbyAjax::requireVerifiedUser();

include_once(__DIR__ . '/handle,admin,blog,generic-post,shared.php');

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

$response->wasSuccessful = true;
// just send a response back that indications the communication worked
$response->message = "Title: {$_POST['title']}";

echo json_encode($response);

ColbyAjax::end();
