<?php // View for a basic blog post with one optional image

$documentTypeId = 'abb85feaa97ca39b1bdb0e8a29359f1995fdcc8d';

$page = new ColbyOutputManager();
$archive = ColbyRequest::$archive;

$page->titleHTML = $archive->valueForKey('titleHTML');
$page->descriptionHTML = $archive->valueForKey('subtitleHTML');

if ($archive->valueForKey('isPublished'))
{
    $publishedTimestamp = $archive->valueForKey('publishedTimeStamp');

    $publishedDataTimestampAttribute =  $publishedTimestamp * 1000;
    $publishedDateTimeAttribute = ColbyConvert::timestampToRFC3339($publishedTimestamp);
    $publishedTextContent = ColbyConvert::timestampToOldBrowserReadableTime($publishedTimestamp);
}
else
{
    $publishedDataTimestampAttribute = '';
    $publishedDateTimeAttribute = '';
    $publishedTextContent = 'not published';
}

$page->begin();

?>

<article>

    <h1><?php echo $archive->valueForKey('titleHTML'); ?></h1>
    <h2><?php echo $archive->valueForKey('subtitleHTML'); ?></h2>

    <?php

    if ($documentImageBasename = $archive->valueForKey('documentImageBasename'))
    {
        $imageURL = $archive->dataURL() . '/' . $documentImageBasename;

        ?>

        <aside>

            <figure>
                <img src="<?php echo $imageURL; ?>"
                     alt="<?php echo $archive->valueForKey('imageAlternativeText'); ?>">

                <figcaption><?php echo $archive->valueForKey('imageCaptionHTML'); ?></figcaption>
            </figure>

            <div class="published">Published:<br>
                <time class="time"
                      datetime="<?php echo $publishedDateTimeAttribute; ?>"
                      data-timestamp="<?php echo $publishedDataTimestampAttribute; ?>">
                    <?php echo $publishedTextContent; ?>
                </time>
            </div>

        </aside>

        <?php
    }

    ?>

    <section class="formatted-content"><?php echo $archive->valueForKey('contentFormattedHTML'); ?></section>

</article>

<?php

done:

$page->end();
