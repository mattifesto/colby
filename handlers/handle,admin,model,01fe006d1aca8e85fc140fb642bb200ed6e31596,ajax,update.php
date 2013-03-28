<?php // Document updater for a document with one image

Colby::useImage();

$response = new ColbyOutputManager('ajax-response');

$response->begin();

if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    $response->message = 'You are not authorized to use this feature.';

    goto done;
}

$archive = ColbyArchive::archiveFromPostData();

$archive->setMarkaroundValueForKey($_POST['content'], 'content');
$archive->setStringValueForKey($_POST['image-caption'], 'imageCaption');
$archive->setStringValueForKey($_POST['image-alternative-text'], 'imageAlternativeText');

$archive->model->setContentSearchText($archive->valueForKey('content'));

if (isset($_FILES['image-file']))
{
    $absoluteMasterImageFilename = ColbyImage::importUploadedImage('image-file', $archive->path());

    // Create an images sized for viewing in the post.

    $size = getimagesize($absoluteMasterImageFilename);

    if ($size[0] > 500)
    {
        $absoluteResizedImageFilename = ColbyImage::createImageByFitting($absoluteMasterImageFilename,
                                                                         array(500, PHP_INT_MAX));
    }
    else
    {
        $absoluteResizedImageFilename = $absoluteMasterImageFilename;
    }

    $archive->setStringValueForKey(basename($absoluteResizedImageFilename), 'imageFilename', false);

    // Create a thumbnail image if the master image is large enough.

    if ($size[0] >= 400 && $size[1] >= 400)
    {
        $absoluteThumbnailImageFilename = ColbyImage::createImageByFilling($absoluteMasterImageFilename,
                                                                           array(400, 400));

        // TODO: Either support png or force jpg.

        rename($absoluteThumbnailImageFilename, $archive->path('thumbnail.jpg'));
    }

    // Delete the master image if we have no need for it.

    if ($absoluteResizedImageFilename != $absoluteMasterImageFilename)
    {
        unlink($absoluteMasterImageFilename);
    }

    $response->imageURL = $archive->url($archive->valueForKey('imageFilename'));
}

$archive->save();

$response->pageStub = $archive->model->pageStub();
$response->wasSuccessful = true;
//$response->message = var_export($_POST, true);
$response->message = 'Post last updated: ' . ColbyConvert::timestampToLocalUserTime(time());

done:

$response->end();
