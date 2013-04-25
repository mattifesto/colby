<?php // View for a basic blog post with one optional image
      // Required parameters: $archive

$documentTypeId = 'abb85feaa97ca39b1bdb0e8a29359f1995fdcc8d';

$page = new ColbyOutputManager();

$page->titleHTML = $archive->valueForKey('titleHTML');
$page->descriptionHTML = $archive->valueForKey('subtitleHTML');

$publishedTimestamp = $archive->valueForKey('isPublished') ? $archive->valueForKey('publishedTimeStamp') * 1000 : '';
$publishedText = $archive->valueForKey('isPublished') ? '' : 'not published';

$page->begin();

?>

<article style="width: 700px; margin: 0px auto;">

    <style scoped>
        article
        {
            padding: 50px 0px;
        }

        h1
        {
            margin-bottom: 0.25em;
            font-size: 1.5em;
        }

        h2
        {
            margin-bottom: 2.0em;
            color: #999999;
            font-size: 1.2em;
            font-weight: 600;
        }

        .posted
        {
            margin-bottom: 15px;
            color: #999999;
            font-size: 0.8em;
            xtext-align: center;
        }

        .formatted-content
        {
            overflow: hidden;
        }
    </style>

    <h1><?php echo $archive->valueForKey('titleHTML'); ?></h1>
    <h2><?php echo $archive->valueForKey('subtitleHTML'); ?></h2>

    <div class="posted">Posted:
        <span class="time"
              data-timestamp="<?php echo $publishedTimestamp; ?>">
            <?php echo $publishedText; ?>
        </span>
    </div>

    <?php

    if ($archive->valueForKey('imageFilename'))
    {
        $absoluteImageURL = $archive->url($archive->valueForKey('imageFilename'));

        ?>

        <img src="<?php echo $absoluteImageURL; ?>" alt="" style="max-width: 250px; margin: 0px 25px 10px 0px; float: left;">

        <?php
    }

    ?>

    <div class="formatted-content"><?php echo $archive->valueForKey('contentFormattedHTML'); ?></div>

</article>

<?php

done:

$page->end();
