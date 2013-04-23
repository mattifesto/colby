<?php // View for a page with an optional image
      // Required parameters: $archive

$documentTypeId = '01fe006d1aca8e85fc140fb642bb200ed6e31596';

$page = new ColbyOutputManager();

$page->titleHTML = $archive->valueForKey('titleHTML');
$page->descriptionHTML = $archive->valueForKey('subtitleHTML');

$page->begin();

?>

<article class="document-type-<?php echo $documentTypeId; ?>">
    <style>
    article
    {
        width: 600px;
        margin: 50px auto;
    }

    h1
    {
        font-size: 1.5em;
        margin-bottom: 0.5em;
    }

    h2
    {
        margin-bottom: 2.0em;
        color: gray;
        font-size: 1.1em;
    }
    </style>

    <h1><?php echo $archive->valueForKey('titleHTML'); ?></h1>
    <h2><?php echo $archive->valueForKey('subtitleHTML'); ?></h2>

    <div class="formatted-content"><?php echo $archive->valueForKey('contentFormattedHTML'); ?></div>

</article>

<?php

done:

$page->end();
