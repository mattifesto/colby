<?php

include_once CBSystemDirectory . '/classes/CBDataStore.php';
include_once CBSystemDirectory . '/classes/ColbyImageUploader.php';


$response = new ColbyOutputManager('ajax-response');

$response->begin();


/**
 *
 */

if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    $response->message = 'You are not authorized to use this feature.';

    goto done;
}


/**
 *
 */

$dataStoreID        = $_POST['dataStoreID'];
$dataStore          = new CBDataStore($dataStoreID);
$uploader           = ColbyImageUploader::uploaderForName('image');
$filename           = Colby::uniqueSHA1Hash() . $uploader->canonicalExtension();
$absoluteFilename   = $dataStore->directory() . "/{$filename}";

$uploader->moveToFilename($absoluteFilename);


/**
 * Send the response
 */

$response->imageFilename    = $filename;
$response->imageSizeX       = $uploader->sizeX();
$response->imageSizeY       = $uploader->sizeY();
$response->wasSuccessful    = true;

done:

$response->end();
