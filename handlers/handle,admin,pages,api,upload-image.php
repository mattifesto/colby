<?php

if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed-ajax.php';
}

$response = new CBAjaxResponse();

/**
 *
 */

$dataStoreID        = $_POST['dataStoreID'];
$dataStore          = new CBDataStore($dataStoreID);
$uploader           = ColbyImageUploader::uploaderForName('image');
$filename           = $uploader->sha1() . '-original' .  $uploader->canonicalExtension();
$absoluteFilename   = $dataStore->directory() . "/{$filename}";

$uploader->moveToFilename($absoluteFilename);


/**
 * Send the response
 */

$response->imageFilename    = $filename;
$response->imageURL         = $dataStore->URL() . "/{$filename}";
$response->imageSizeX       = $uploader->sizeX();
$response->imageSizeY       = $uploader->sizeY();
$response->wasSuccessful    = true;

$response->send();
