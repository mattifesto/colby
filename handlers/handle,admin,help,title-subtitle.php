<?php

include_once CBSystemDirectory . '/classes/CBHTMLOutput.php';


CBHTMLOutput::setTitleHTML('Titles and Descriptions Help');
CBHTMLOutput::setDescriptionHTML('Help for creating effective titles and descriptions.');
CBHTMLOutput::begin();


if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    include Colby::findSnippet('authenticate.php');

    goto done;
}


include CBSystemDirectory . '/sections/admin-page-header.php';

$selectedMenuItemID     = 'help';
$selectedSubmenuItemID  = 'title-subtitle';

include CBSystemDirectory . '/sections/admin-page-menu.php';

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

include CBSystemDirectory . '/sections/admin-page-footer.php';

CBHTMLOutput::render();
