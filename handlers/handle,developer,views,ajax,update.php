<?php

$response = ColbyOutputManager::beginVerifiedUserAjaxResponse();

$viewId = $_POST['view-id'];

$data = new stdClass();
$data->name = $_POST['name'];
$data->nameHTML = ColbyConvert::textToHTML($data->name);
$data->description = $_POST['description'];
$data->descriptionHTML = ColbyConvert::textToFormattedContent($data->description);
$data->modelId = $_POST['model-id'];
$data->groupId = $_POST['group-id'];

$handlerFilenameBase = "handle,admin,view,{$viewId}";

$absoluteDataFilename = Colby::findHandler("{$handlerFilenameBase}.data");

if (!$absoluteDataFilename)
{
    // If a data file doesn't yet exists will go through a more detailed process:
    //
    // 1. Make sure the site specific handlers directory exists.
    // 2. Create all the necessary template files for the view, if they don't exist.
    // 3. Finally write the data file (happens outside this block, in all cases).

    $absoluteHandlersDirectory = COLBY_SITE_DIRECTORY . '/handlers';
    $absoluteHandlerFilenameBase = "{$absoluteHandlersDirectory}/{$handlerFilenameBase}";

    $absoluteDataFilename       = "{$absoluteHandlerFilenameBase}.data";
    $absoluteViewFilename    = "{$absoluteHandlerFilenameBase}.php";

    // 1. Make sure the site specific handlers directory exists.

    if (!file_exists($absoluteHandlersDirectory))
    {
        mkdir($absoluteHandlersDirectory);
    }

    // 2. Create all the necessary template files for the view, if they don't exist.

    if (!file_exists($absoluteViewFilename))
    {
        touch($absoluteViewFilename);
    }
}

// 3. Write the data file.

file_put_contents($absoluteDataFilename, serialize($data));

$response->wasSuccessful = true;
$response->message = 'The view was successfully updated.';

$response->end();
