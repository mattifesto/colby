<?php

/**
 * This is the model updater for a model with a title, subtitle, and content.
 */

$response = new ColbyOutputManager('ajax-response');

$response->begin();

if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    $response->message = 'You are not authorized to use this feature.';

    goto done;
}

$archive = ColbyArchive::archiveFromPostData();

$archive->setMarkaroundValueForKey($_POST['content'], 'content');

$archive->model->setContentSearchText($archive->valueForKey('content'));

$archive->save();

$response->pageStub = $archive->model->pageStub();
$response->wasSuccessful = true;
//$response->message = var_export($_POST, true);
$response->message = 'Post last updated: ' . ColbyConvert::timestampToLocalUserTime(time());

done:

$response->end();
