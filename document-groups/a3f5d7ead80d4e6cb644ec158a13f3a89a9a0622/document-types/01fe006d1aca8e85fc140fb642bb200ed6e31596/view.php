<?php // View COLBY_PAGES_DOCUMENT_GROUP_ID -> COLBY_PAGE_DOCUMENT_TYPE_ID

$archive = ColbyRequest::$archive;

CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML($archive->valueForKey('titleHTML'));
CBHTMLOutput::setDescriptionHTML($archive->valueForKey('subtitleHTML'));

include Colby::findFile('sections/public-page-settings.php');
include Colby::findFile('sections/standard-page-header.php');

?>

<article class="document-type-<?php echo COLBY_PAGE_DOCUMENT_TYPE_ID; ?>">
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

include Colby::findFile('sections/standard-page-footer.php');

CBHTMLOutput::render();
