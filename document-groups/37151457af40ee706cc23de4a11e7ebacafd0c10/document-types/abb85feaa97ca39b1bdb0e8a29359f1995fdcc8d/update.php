<?php // Document updater for a basic blog post with one optional image

if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    include Colby::findHandler('handle-authorization-failed-ajax.php');

    exit;
}

Colby::useImage();

$response = new ColbyOutputManager('ajax-response');

$response->begin();

$archive = ColbyArchive::open($_POST['archive-id']);

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
 * Define the search text.
 */

$searchText = array();

$searchText[] = $_POST['title'];
$searchText[] = $_POST['subtitle'];
$searchText[] = $_POST['content'];
$searchText[] = $_POST['image-caption'];
$searchText[] = $_POST['image-alternative-text'];

$searchText = implode(' ', $searchText);

/**
 * Make sure the URI is available before saving as the 'uri' value in the
 * archive. This way the database and the archive values will always be in sync.
 */

$response->uriIsAvailable = updateDatabase($archive, $_POST['uri'], $searchText);

if ($response->uriIsAvailable)
{
    $archive->setStringValueForKey($_POST['uri'], 'uri', false);
}

/**
 *
 */

if (isset($_FILES['image-file']))
{
    $absoluteMasterImageFilename = ColbyImage::importUploadedImage('image-file', $archive->absoluteDataDirectory());

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

        rename($absoluteThumbnailImageFilename, $archive->absoluteDataDirectory() . '/thumbnail.jpg');
    }

    // Delete the master image if we have no need for it.

    if ($absoluteResizedImageFilename != $absoluteMasterImageFilename)
    {
        unlink($absoluteMasterImageFilename);
    }

    $response->imageURL = $archive->dataURL() . '/' . $archive->valueForKey('imageFilename');
}

$archive->save();

$response->wasSuccessful = true;
$response->message = 'Post last updated: ' . ColbyConvert::timestampToLocalUserTime(time());

done:

$response->end();


/**
 * @return bool
 *  `true` if successful
 *  `false` if the provided URI is not available
 */
function updateDatabase($archive, $uri, $searchText)
{
    $mysqli = Colby::mysqli();

    $archiveId = $archive->archiveId();
    $titleHTML = $mysqli->escape_string($archive->valueForKey('titleHTML'));
    $subtitleHTML = $mysqli->escape_string($archive->valueForKey('subtitleHTML'));
    $searchText = $mysqli->escape_string($searchText);

    if ($archive->valueForKey('isPublished'))
    {
        $published = $archive->valueForKey('publishedTimeStamp');
    }
    else
    {
        $published = 'NULL';
    }

    $publishedBy = intval($archive->valueForKey('publishedBy'));

    if (!$publishedBy)
    {
        $publishedBy = 'NULL';
    }

    $sql = <<<EOT
SELECT
    `id`
FROM
    `ColbyPages`
WHERE
    `archiveId` = UNHEX('{$archiveId}')
EOT;

    $result = Colby::query($sql);

    if ($row = $result->fetch_object())
    {
        $id = $row->id;
    }
    else
    {
        $id = null;
    }

    $result->free();

    if ($id)
    {
        $sql = <<<EOT
UPDATE
    `ColbyPages`
SET
    `stub` = '{$uri}',
    `titleHTML` = '{$titleHTML}',
    `subtitleHTML` = '{$subtitleHTML}',
    `searchText` = '{$searchText}',
    `published` = {$published},
    `publishedBy` = {$publishedBy}
WHERE
    `id` = {$id}
EOT;

        try
        {
            Colby::query($sql);
        }
        catch (Exception $exception)
        {
            if (1062 == $mysqli->errno)
            {
                return false;
            }
            else
            {
                throw $exception;
            }
        }
    }
    else
    {
        $sql = <<<EOT
INSERT INTO
    `ColbyPages`
(
    `archiveId`,
    `groupId`,
    `modelId`,
    `stub`,
    `titleHTML`,
    `subtitleHTML`,
    `searchText`,
    `published`,
    `publishedBy`
)
VALUES
(
    UNHEX('{$archiveId}'),
    UNHEX('{$archive->valueForKey('documentGroupId')}'),
    UNHEX('{$archive->valueForKey('documentTypeId')}'),
    '{$uri}',
    '{$titleHTML}',
    '{$subtitleHTML}',
    '{$searchText}',
    {$published},
    {$publishedBy}
)
EOT;

        try
        {
            Colby::query($sql);
        }
        catch (Exception $exception)
        {
            if (1062 == $mysqli->errno)
            {
                return false;
            }
            else
            {
                throw $exception;
            }
        }
    }

    return true;
}
