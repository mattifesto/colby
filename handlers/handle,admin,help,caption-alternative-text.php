<?php

$page = new ColbyOutputManager('admin-html-page');

$page->titleHTML = 'Caption and Alternative Text';
$page->descriptionHTML = 'Help for creating effective captions and alternative text.';

$page->begin();

if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    include Colby::findSnippet('authenticate.php');

    goto done;
}

$markaround = file_get_contents(COLBY_SITE_DIRECTORY . '/colby/snippets/help/caption-alternative-text.txt');

?>

<main>
    <h1>Caption and Alternative Text Help</h1>

    <div style="font-size: 14px;">
        <div class="formatted-content standard-formatted-content">
            <?php echo ColbyConvert::markaroundToHTML($markaround); ?>
        </div>
    </div>
</main>

<?php

done:

$page->end();
