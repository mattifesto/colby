<?php

/**
 * This is the model updater for a model with a title, subtitle, and content.
 */

$response = ColbyOutputManager::beginVerifiedUserAjaxResponse();

$archive = ColbyArchive::archiveFromPostData();

$archive->setMarkaroundValueForKey($_POST['content'], 'content');

$archive->model->setContentSearchText($archive->valueForKey('content'));

$archive->save();

$response->pageStub = $archive->model->pageStub();
$response->wasSuccessful = true;
//$response->message = var_export($_POST, true);
$response->message = 'Post last updated: ' . ColbyConvert::timestampToLocalUserTime(time());

$response->end();
