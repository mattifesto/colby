<?php

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed-ajax.php';
}

$response = new CBAjaxResponse();

/**
 * Make sure the destination directory is available
 */

$location = $_POST['location'];
$documentGroupId = $_POST['document-group-id'];

$documentGroupDirectory = COLBY_SITE_DIRECTORY .
                          "/{$location}" .
                          "/document-groups/{$documentGroupId}";

if (!is_dir($documentGroupDirectory))
{
    mkdir($documentGroupDirectory, 0777, true);
}

/**
 * Load the current data file if it exists
 */

$documentGroupDataFilename = "{$documentGroupDirectory}/document-group.data";

if (is_file($documentGroupDataFilename))
{
    $data = unserialize(file_get_contents($documentGroupDataFilename));
}
else
{
    $data = new stdClass();
}

/**
 * Update the data object
 */

$data->id = $documentGroupId;
$data->libraryDirectory = $location;

$updated = time();

if (!isset($data->created))
{
    $data->created = $updated;
}

$data->updated = $updated;
$data->name = $_POST['name'];
$data->nameHTML = ColbyConvert::textToHTML($data->name);
$data->description = $_POST['description'];
$data->descriptionHTML = ColbyConvert::markaroundToHTML($data->description);
$data->stub = $_POST['stub'];

/**
 * Write the data file
 */

file_put_contents($documentGroupDataFilename, serialize($data));

/**
 * Send the response
 */

$response->wasSuccessful = true;
$response->message = 'The group was successfully updated.';

$response->send();
