<?php // View for a page with an optional image
      // Required parameters: $archive

$page = new ColbyOutputManager();

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
