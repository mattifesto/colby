<?php

$page = new ColbyOutputManager('admin-html-page');

$page->titleHTML = 'Title and Subtitle';
$page->descriptionHTML = 'Help for creating effective titles and subtitles.';

$page->begin();

if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    include Colby::findSnippet('authenticate.php');

    goto done;
}

$markaround = file_get_contents(COLBY_SITE_DIRECTORY . '/colby/snippets/help/title-subtitle.txt');

?>

<main>
    <h1>Title and Subtitle Help</h1>

    <div style="font-size: 14px;">
        <div class="formatted-content standard-formatted-content">
            <?php echo ColbyConvert::markaroundToHTML($markaround); ?>
        </div>
    </div>
</main>

<?php

done:

$page->end();
