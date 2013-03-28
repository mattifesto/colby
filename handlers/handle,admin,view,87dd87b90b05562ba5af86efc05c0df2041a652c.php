<?php // Document for a blog post with content and one 250px wide image, works best for portrait images

$page = ColbyOutputManager();

if (isset($_GET['action']) && 'preview' == $_GET['action'])
{
    if (!ColbyUser::current()->isOneOfThe('Administrators'))
    {
        include Colby::findSnippet('authenticate.php');

        goto done;
    }

    $archive = ColbyArchive::open($_GET['archive-id']);
}

$page->titleHTML = $archive->valueForKey('titleHTML');
$page->descriptionHTML = $archive->valueForKey('subtitleHTML');

$publicationTimestamp = $archive->valueForKey('isPublished') ? $archive->valueForKey('publicationDate') * 1000 : '';
$publicationText = $archive->valueForKey('isPublished') ? '' : 'not published';

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
              data-timestamp="<?php echo $publicationTimestamp; ?>">
            <?php echo $publicationText; ?>
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

    <div class="formatted-content"><?php echo $archive->valueForKey('contentHTML'); ?></div>

</article>

<?php

done:

$page->end();
