<?php

$response = new ColbyOutputManager('ajax-response');

$response->begin();

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    $response->message = 'You are not authorized to use this feature.';

    goto done;
}

/**
 * Make sure the destination directory is available
 */

$location = $_POST['location'];
$groupId = $_POST['group-id'];

$groupDirectory = COLBY_SITE_DIRECTORY . "/{$location}/document-groups/{$groupId}";
$groupDataFilename = "{$groupDirectory}/group.data";

if (!is_dir($groupDirectory))
{
    mkdir($groupDirectory, 0777, true);
}

/**
 * Create the data object
 */

$data = new stdClass();
$data->name = $_POST['name'];
$data->nameHTML = ColbyConvert::textToHTML($data->name);
$data->description = $_POST['description'];
$data->descriptionHTML = ColbyConvert::markaroundToHTML($data->description);
$data->stub = $_POST['stub'];

/**
 * Write the data file
 */

file_put_contents($groupDataFilename, serialize($data));

/**
 * Send response
 */

$response->wasSuccessful = true;
$response->message = 'The group was successfully updated.';

done:

$response->end();
