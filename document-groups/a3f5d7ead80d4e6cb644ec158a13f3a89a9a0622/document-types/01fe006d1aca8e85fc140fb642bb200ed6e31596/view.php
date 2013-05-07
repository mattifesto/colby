<?php // View for a basic page with one optional image

$documentTypeId = '01fe006d1aca8e85fc140fb642bb200ed6e31596';

$page = new ColbyOutputManager();
$archive = ColbyRequest::$archive;

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

    <header>
        <h1><?php echo $archive->valueForKey('titleHTML'); ?></h1>
        <h2><?php echo $archive->valueForKey('subtitleHTML'); ?></h2>
    </header>

    <?php

    if ($documentImageBasename = $archive->valueForKey('documentImageBasename'))
    {
        $imageURL = $archive->dataURL() . '/' . $documentImageBasename;

        ?>

        <img src="<?php echo $imageURL; ?>"
             alt="<?php echo $archive->valueForKey('imageAlternativeText'); ?>"
             style="max-width: 250px; margin: 0px 0px 10px 25px; float: right;">

        <?php
    }

    ?>

    <section class="formatted-content"><?php echo $archive->valueForKey('contentFormattedHTML'); ?></section>

</article>

<?php

done:

$page->end();
