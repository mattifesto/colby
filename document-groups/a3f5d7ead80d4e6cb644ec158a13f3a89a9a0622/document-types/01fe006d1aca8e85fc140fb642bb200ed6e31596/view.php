<?php // View for a page with no images

$page = ColbyOutputManager();

if (isset($_GET['action']) && 'preview' == $_GET['action'])
{
    if (!ColbyUser::current()->isOneOfThe('Administrators'))
    {
        $page->titleHTML = 'Authorization Required';
        $page->descriptionHTML = 'You are not authorized to view this page.';

        $page->begin();

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
