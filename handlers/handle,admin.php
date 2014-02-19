<?php

include(Colby::findHandler('handle-ensure-installation.php'));

include_once CBSystemDirectory . '/classes/CBHTMLOutput.php';


CBHTMLOutput::setTitleHTML('Status');
CBHTMLOutput::setDescriptionHTML('The status of the website');
CBHTMLOutput::begin();


if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    include Colby::findSnippet('authenticate.php');

    goto done;
}


include CBSystemDirectory . '/sections/admin-page-header.php';

$selectedMenuItemID     = 'general';
$selectedSubmenuItemID  = 'status';

include CBSystemDirectory . '/sections/admin-page-menu.php';

CBHTMLOutput::addCSSURL(CBSystemURL . '/handlers/handle,admin.css');

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


done:

include CBSystemDirectory . '/sections/admin-page-footer.php';

CBHTMLOutput::render();
