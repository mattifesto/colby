<?php

$response = ColbyOutputManager::beginVerifiedUserAjaxResponse();

$groupId = $_POST['group-id'];

$data = new stdClass();
$data->name = $_POST['name'];
$data->nameHTML = ColbyConvert::textToHTML($data->name);
$data->description = $_POST['description'];
$data->descriptionHTML = ColbyConvert::textToFormattedContent($data->description);
$data->stub = $_POST['stub'];

$handlerFilenameBase = "handle,admin,group,{$groupId}";

$absoluteDataFilename = Colby::findHandler("{$handlerFilenameBase}.data");

if (!$absoluteDataFilename)
{
    // If a data file doesn't yet exists will go through a more detailed process:
    //
    // 1. Make sure the site specific handlers directory exists.
    // 2. Write the data file (happens outside this block, in all cases).

    $absoluteHandlersDirectory = COLBY_SITE_DIRECTORY . '/handlers';
    $absoluteHandlerFilenameBase = "{$absoluteHandlersDirectory}/{$handlerFilenameBase}";

    $absoluteDataFilename       = "{$absoluteHandlerFilenameBase}.data";

    // 1. Make sure the site specific handlers directory exists.

    if (!file_exists($absoluteHandlersDirectory))
    {
        mkdir($absoluteHandlersDirectory);
    }
}

// 2. Write the data file.

file_put_contents($absoluteDataFilename, serialize($data));

$response->wasSuccessful = true;
$response->message = 'The group was successfully updated.';

$response->end();
