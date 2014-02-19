<?php

include_once CBSystemDirectory . '/classes/CBHTMLOutput.php';


CBHTMLOutput::setTitleHTML('Captions and Alternative Text Help');
CBHTMLOutput::setDescriptionHTML('Help for creating effective captions and alternative text.');
CBHTMLOutput::begin();


if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    include Colby::findSnippet('authenticate.php');

    goto done;
}


include CBSystemDirectory . '/sections/admin-page-header.php';

$selectedMenuItemID     = 'help';
$selectedSubmenuItemID  = 'caption-alternative-text';

include CBSystemDirectory . '/sections/admin-page-menu.php';

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

include CBSystemDirectory . '/sections/admin-page-footer.php';

CBHTMLOutput::render();
