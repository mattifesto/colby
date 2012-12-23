<?php

/**
 * Blog post
 * Title, subtitle, formatted content, medium image floated right
 * More appropriate for vertical images
 */

if (isset($archive))
{
    $page = ColbyOutputManager::beginPage($archive->valueForKey('titleHTML'),
                                          $archive->valueForKey('subtitleHTML'));
}
else
{
    // If this URL handler is called directly, it's because a verified user wants to preview a post.
    // So we get the post and display the page, but only for verified users.
    // This method will display both published and unpublished posts.
    // TODO: Can this work be generalized for all types of blog posts?

    if (!isset($_GET['archive-id']))
    {
        return false;
    }

    $archive = ColbyArchive::open($_GET['archive-id']);

    $page = ColbyOutputManager::beginVerifiedUserPage($archive->valueForKey('titleHTML'),
                                                      $archive->valueForKey('subtitleHTML'));
}

$publicationTimestamp = $archive->valueForKey('isPublished') ? $archive->valueForKey('publicationDate') * 1000 : '';
$publicationText = $archive->valueForKey('isPublished') ? '' : 'not published';

?>

<article style="width: 600px; margin: 0px auto;">
    <h1><?php echo $archive->valueForKey('titleHTML'); ?></h1>
    <h2><?php echo $archive->valueForKey('subtitleHTML'); ?></h2>
    <div style="margin-bottom: 20px;">Posted:
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

        <img src="<?php echo $absoluteImageURL; ?>" alt="" style="max-width: 250px; margin: 0px 0px 10px 20px; float: right;">

        <?php
    }

    ?>

    <div class="formatted-content"><?php echo $archive->valueForKey('contentHTML'); ?></div>
</article>

<?php

$page->end();
