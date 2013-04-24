<?php // View for a basic blog post with one optional image
      // Required parameters: $archive

$documentTypeId = 'abb85feaa97ca39b1bdb0e8a29359f1995fdcc8d';

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
