<?php

$response = ColbyOutputManager::beginVerifiedUserAjaxResponse();

$blogPostTypeId = $_POST['blog-post-type-id'];

$data = new stdClass();
$data->name = $_POST['name'];
$data->nameHTML = ColbyConvert::textToHTML($data->name);
$data->description = $_POST['description'];
$data->descriptionHTML = ColbyConvert::textToFormattedContent($data->description);

$handlerFilenameBase = "handle,admin,blog,{$blogPostTypeId}";

$absoluteDataFilename = Colby::findHandler("{$handlerFilenameBase}.data");

if (!$absoluteDataFilename)
{
    // If a data file doesn't yet exists will go through a more detailed process:
    //
    // 1. Make sure the site specific handlers directory exists.
    // 2. Create all the necessary template files for the blog post type, if they don't exist.
    // 3. Finally write the data file (happens outside this block, in all cases).

    $absoluteHandlersDirectory = COLBY_SITE_DIRECTORY . '/handlers';
    $absoluteHandlerFilenameBase = "{$absoluteHandlersDirectory}/{$handlerFilenameBase}";

    $absoluteDataFilename       = "{$absoluteHandlerFilenameBase}.data";
    $absoluteDisplayFilename    = "{$absoluteHandlerFilenameBase},display.php";
    $absoluteEditFilename       = "{$absoluteHandlerFilenameBase},edit.php";
    $absoluteUpdateFilename     = "{$absoluteHandlerFilenameBase},ajax,update.php";

    // 1. Make sure the site specific handlers directory exists.

    if (!file_exists($absoluteHandlersDirectory))
    {
        mkdir($absoluteHandlersDirectory);
    }

    // 2. Create all the necessary template files for the blog post type, if they don't exist.

    if (!file_exists($absoluteDisplayFilename))
    {
        touch($absoluteDisplayFilename);
    }

    if (!file_exists($absoluteEditFilename))
    {
        touch($absoluteEditFilename);
    }

    if (!file_exists($absoluteUpdateFilename))
    {
        touch($absoluteUpdateFilename);
    }
}

// 3. Write the data file.

file_put_contents($absoluteDataFilename, serialize($data));

$response->wasSuccessful = true;
$response->message = 'The blog post type was successfully updated.';

$response->end();
