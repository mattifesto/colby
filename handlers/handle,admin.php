<?php

include(Colby::findHandler('handle-ensure-installation.php'));

include_once CBSystemDirectory . '/classes/CBHTMLOutput.php';


if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}


CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML('Status');
CBHTMLOutput::setDescriptionHTML('The status of the website');

include CBSystemDirectory . '/sections/equalize.php';

CBHTMLOutput::addCSSURL('https://fonts.googleapis.com/css?family=Source+Sans+Pro:400');
CBHTMLOutput::addCSSURL('https://fonts.googleapis.com/css?family=Source+Sans+Pro:600');
CBHTMLOutput::addCSSURL('https://fonts.googleapis.com/css?family=Source+Sans+Pro:700');
CBHTMLOutput::addCSSURL(CBSystemURL . '/handlers/handle,admin.css');


$selectedMenuItemID     = 'general';
$selectedSubmenuItemID  = 'status';

include CBSystemDirectory . '/sections/admin-page-menu.php';

?>

<main>

    <?php

    $adminWidgetFilenames = Colby::globSnippets('admin-widget-*.php');

    foreach ($adminWidgetFilenames as $adminWidgetFilename)
    {
        include $adminWidgetFilename;
    }

    ?>

</main>

<?php

include CBSystemDirectory . '/sections/admin-page-footer-2.php';

CBHTMLOutput::render();
