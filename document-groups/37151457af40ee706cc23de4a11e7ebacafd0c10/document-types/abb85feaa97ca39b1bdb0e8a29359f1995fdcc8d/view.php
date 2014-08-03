<?php // View COLBY_BLOG_POSTS_DOCUMENT_GROUP_ID -> COLBY_BLOG_POST_DOCUMENT_TYPE_ID

$archive = ColbyRequest::$archive;

CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML($archive->valueForKey('titleHTML'));
CBHTMLOutput::setDescriptionHTML($archive->valueForKey('subtitleHTML'));

include Colby::findFile('sections/public-page-settings.php');
include Colby::findFile('sections/standard-page-header.php');

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

?>

<article class="centered-block standard-white-space blog-post">
    <style scoped>

        article.blog-post
        {
            width: 600px;
        }

    </style>

    <header class="standard-document-header">
        <h1><?php echo $archive->valueForKey('titleHTML'); ?></h1>
        <h2><?php echo $archive->valueForKey('subtitleHTML'); ?></h2>
    </header>

    <aside>

        <?php

        if ($documentImageBasename = $archive->valueForKey('documentImageBasename'))
        {
            $imageURL = $archive->dataURL() . '/' . $documentImageBasename;

            ?>

                <figure>

                    <img src="<?php echo $imageURL; ?>"
                         alt="<?php echo $archive->valueForKey('imageAlternativeText'); ?>">

                    <figcaption><?php echo $archive->valueForKey('imageCaptionHTML'); ?></figcaption>

                </figure>

            <?php
        }

        ?>

        <div class="pair published">
            <span class="key">Published</span>
            <time class="value time"
                  datetime="<?php echo $publishedDateTimeAttribute; ?>"
                  data-timestamp="<?php echo $publishedDataTimestampAttribute; ?>">
                <?php echo $publishedTextContent; ?>
            </time>
        </div>

    </aside>

    <section class="formatted-content standard-formatted-content"><?php echo $archive->valueForKey('contentFormattedHTML'); ?></section>

</article>

<?php

include Colby::findFile('sections/standard-page-footer.php');

CBHTMLOutput::render();
