<?php

if (isset($archive))
{
    $page = ColbyOutputManager::beginPage($archive->rootObject()->titleHTML,
                                          $archive->rootObject()->subtitleHTML);
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

    $page = ColbyOutputManager::beginVerifiedUserPage($archive->rootObject()->titleHTML,
                                                      $archive->rootObject()->subtitleHTML);
}

?>

<h1><?php echo $archive->rootObject()->titleHTML; ?></h1>
<h2><?php echo $archive->rootObject()->subtitleHTML; ?></h2>

<div class="formatted-content"><?php echo $archive->rootObject()->contentHTML; ?></div>

<?php

$page->end();
