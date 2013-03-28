<?php // Document for a page with content and no images

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

?>

<article>

    <h1><?php echo $archive->valueForKey('titleHTML'); ?></h1>
    <h2><?php echo $archive->valueForKey('subtitleHTML'); ?></h2>

    <div class="formatted-content"><?php echo $archive->valueForKey('contentHTML'); ?></div>

</article>

<?php

done:

$page->end();
