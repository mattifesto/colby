<?php

include_once CBSystemDirectory . '/classes/CBHTMLOutput.php';


if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}


CBHTMLOutput::setTitleHTML('Markaround Help');
CBHTMLOutput::setDescriptionHTML('Help for markaround syntax.');
CBHTMLOutput::begin();

include CBSystemDirectory . '/sections/equalize.php';

CBHTMLOutput::addCSSURL(CBSystemURL . '/css/standard-formatted-content.css');
CBHTMLOutput::addCSSURL('https://fonts.googleapis.com/css?family=Source+Sans+Pro:400');
CBHTMLOutput::addCSSURL('https://fonts.googleapis.com/css?family=Source+Sans+Pro:700');


$selectedMenuItemID     = 'help';
$selectedSubmenuItemID  = 'markaround-syntax';

include CBSystemDirectory . '/sections/admin-page-menu.php';

$markaround = file_get_contents(COLBY_SITE_DIRECTORY . '/colby/snippets/help/markaround.txt');

?>

<main>
    <style scoped>

        main
        {
            font-family:    "Source Sans Pro";
            margin:         0px auto;
            width:          640px;
        }

    </style>

    <div class="formatted-content standard-formatted-content">
        <?php echo ColbyConvert::markaroundToHTML($markaround); ?>
    </div>
</main>

<?php

include CBSystemDirectory . '/sections/admin-page-footer-2.php';

CBHTMLOutput::render();
