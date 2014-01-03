<?php

$page = new ColbyOutputManager('admin-html-page');

$page->titleHTML = 'Markaround Help';
$page->descriptionHTML = 'Help for markaround syntax.';

$page->begin();

if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    include Colby::findSnippet('authenticate.php');

    goto done;
}

$markaround = file_get_contents(COLBY_SITE_DIRECTORY . '/colby/snippets/help/markaround.txt');

?>

<main>
    <h1>Markaround Help</h1>

    <div style="font-size: 14px;">
        <div class="formatted-content standard-formatted-content">
            <?php echo ColbyConvert::markaroundToHTML($markaround); ?>
        </div>
    </div>
</main>

<?php

done:

$page->end();
