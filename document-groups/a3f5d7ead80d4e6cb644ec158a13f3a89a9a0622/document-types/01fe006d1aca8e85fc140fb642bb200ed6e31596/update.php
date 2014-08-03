<?php // Update COLBY_PAGES_DOCUMENT_GROUP_ID -> COLBY_PAGE_DOCUMENT_TYPE_ID

if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed-ajax.php';
}

Colby::useImage();

$response = new CBAjaxResponse();

$document   = ColbyDocument::documentWithArchiveId($_POST['archive-id']);
$archive    = $document->archive();


/**
 * The first thing we do is attempt to reserve the requested URI in the
 * `ColbyDocuments` table and then set it as the value for the 'uri' key in the
 * archive. If it is successfully reserved (or if it was reserved by a previous
 * request) we continue updating the document. If it is not successfully
 * reserved this request fails and the caller should send another request
 * with another URI. Without a valid and unique URI, we can't successfully save
 * a document.
 */

try
{
    $document->setURI($_POST['uri']);

    $response->uriIsAvailable = true;
}
catch (Exception $exception)
{
    if (1062 == Colby::mysqli()->errno)
    {
        $response->uriIsAvailable = false;
    }
    else
    {
        throw $exception;
    }
}

/**
 *
 */

$archive->setStringValueForKey($_POST['document-group-id'], 'documentGroupId');
$archive->setStringValueForKey($_POST['document-type-id'], 'documentTypeId');

$archive->setStringValueForKey($_POST['title'], 'title');
$archive->setStringValueForKey(ColbyConvert::textToHTML($_POST['title']), 'titleHTML');

$archive->setStringValueForKey($_POST['subtitle'], 'subtitle');
$archive->setStringValueForKey(ColbyConvert::textToHTML($_POST['subtitle']), 'subtitleHTML');

$archive->setBoolValueForKey($_POST['uri-is-custom'], 'uriIsCustom');
$archive->setBoolValueForKey($_POST['is-published'], 'isPublished');
$archive->setIntValueForKey($_POST['published-by'], 'publishedBy');
$archive->setIntValueForKey($_POST['published-time-stamp'], 'publishedTimeStamp');

$archive->setStringValueForKey($_POST['content'], 'content');
$archive->setStringValueForKey(ColbyConvert::markaroundToHTML($_POST['content']), 'contentFormattedHTML');

$archive->setStringValueForKey($_POST['image-caption'], 'imageCaption');
$archive->setStringValueForKey(ColbyConvert::textToHTML($_POST['image-caption']), 'imageCaptionHTML');

$archive->setStringValueForKey($_POST['image-alternative-text'], 'imageAlternativeText');
$archive->setStringValueForKey(ColbyConvert::textToHTML($_POST['image-alternative-text']), 'imageAlternativeTextHTML');


/**
 * The search text is set as a property on the `ColbyArchive` to be stored in
 * the `ColbyDocuments` table when the archive is saved.
 */

$searchText = array();

$searchText[] = $_POST['title'];
$searchText[] = $_POST['subtitle'];
$searchText[] = $_POST['content'];
$searchText[] = $_POST['image-caption'];
$searchText[] = $_POST['image-alternative-text'];

$archive->searchText = implode(' ', $searchText);


/**
 * 2013.05.02
 *
 * This code is just to fix up the pages on merrychristine.com. Once those
 * pages are updated this code should be removed.
 */

if ($documentImageBasename = $archive->valueForKey('imageFilename'))
{
    $archive->setStringValueForKey($documentImageBasename, 'documentImageBasename');

    $archive->unsetValueForKey('imageFilename');
}

/**
 * Process a new image file if one is included with the request.
 */

if (isset($_FILES['image-file']))
{
    /**
     * Complete the image file upload.
     */

    $uploader = ColbyImageUploader::uploaderForName('image-file');

    $originalImageFilename = $archive->absoluteDataDirectory() .
                '/' .
                Colby::uniqueSHA1Hash() .
                $uploader->canonicalExtension();

    $uploader->moveToFilename($originalImageFilename);

    if ($originalImageBasename = $archive->valueForKey('originalImageBasename'))
    {
        $filename = $archive->absoluteDataDirectory() . '/' . $originalImageBasename;

        if (is_file($filename))
        {
            unlink($filename);
        }
    }

    $archive->setStringValueForKey(basename($originalImageFilename), 'originalImageBasename');


    /**
     * Create the version of the image to be displayed in the document.
     */

    $resizer = ColbyImageResizer::resizerForFilename($originalImageFilename);

    $documentImageFilename = $archive->absoluteDataDirectory() .
                '/' .
                Colby::uniqueSHA1Hash() .
                $resizer->canonicalExtension();

    $resizer->reduceWidthTo(500);
    $resizer->saveToFilename($documentImageFilename);

    if ($documentImageBasename = $archive->valueForKey('documentImageBasename'))
    {
        $filename = $archive->absoluteDataDirectory() . '/' . $documentImageBasename;

        if (is_file($filename))
        {
            unlink($filename);
        }
    }

    $archive->setStringValueForKey(basename($documentImageFilename), 'documentImageBasename');


    /**
     * Create the version of the image to be used as the thumbnail.
     */

    $thumbnailImageFilename = $archive->absoluteDataDirectory() .
                '/thumbnail' .
                $resizer->canonicalExtension();

    $resizer->reset();
    $resizer->reduceShortEdgeTo(400);
    $resizer->cropFromCenterToWidth(400);
    $resizer->cropFromCenterToHeight(400);
    $resizer->saveToFilename($thumbnailImageFilename);

    if ($thumbnailImageBasename = $archive->valueForKey('thumbnailImageBasename'))
    {
        $filename = $archive->absoluteDataDirectory() . '/' . $thumbnailImageBasename;

        if ($filename != $thumbnailImageFilename &&
            is_file($filename))
        {
            unlink($filename);
        }
    }

    $archive->setStringValueForKey(basename($thumbnailImageFilename), 'thumbnailImageBasename');

    $thumbnailURL = $archive->dataURL() . '/' . $archive->valueForKey('thumbnailImageBasename');

    $archive->setStringValueForKey($thumbnailURL, 'thumbnailURL');


    /**
     * Return the document image URL for the editor to display.
     */

    $response->imageURL = $archive->dataURL() . '/' . $archive->valueForKey('documentImageBasename');
}

$document->save();

$response->wasSuccessful = true;
$response->message = 'Page successfully updated.';

$response->send();
