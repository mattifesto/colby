<?php

/**
 * This is the built-in view for a page with a title, subtitle, and content.
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

?>

<h1><?php echo $archive->valueForKey('titleHTML'); ?></h1>
<h2><?php echo $archive->valueForKey('subtitleHTML'); ?></h2>

<div class="formatted-content"><?php echo $archive->valueForKey('contentHTML'); ?></div>

<?php

$page->end();
