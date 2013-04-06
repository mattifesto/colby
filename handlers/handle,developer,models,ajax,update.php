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
$documentGroupId = $_POST['document-group-id'];
$documentTypeId = $_POST['document-type-id'];

$documentTypeDirectory = COLBY_SITE_DIRECTORY .
                         "/{$location}/" .
                         "/document-groups/{$documentGroupId}" .
                         "/document-types/{$documentTypeId}";

if (!is_dir($documentTypeDirectory))
{
    mkdir($documentTypeDirectory, 0777, true);
}

/**
 * Load the current data file if it exists
 */

$documentTypeDataFilename = "{$documentTypeDirectory}/document-type.data";

if (is_file($documentTypeDataFilename))
{
    $data = unserialize(file_get_contents($documentTypeDataFilename));
}
else
{
    $data = new stdClass();
}

/**
 * Update the data object
 */

$data->id = $documentTypeId;
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

/**
 * Write the data file
 */

file_put_contents($documentTypeDataFilename, serialize($data));

/**
 * Send the response
 */

$response->wasSuccessful = true;
$response->message = 'The document type was successfully updated.';

done:

$response->end();
