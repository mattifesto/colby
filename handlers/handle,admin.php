<?php

include(Colby::findHandler('handle-ensure-installation.php'));

if (!ColbyUser::current()->isOneOfThe('Administrators')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML('Status');
CBHTMLOutput::setDescriptionHTML('The status of the website');

include CBSystemDirectory . '/sections/admin-page-settings.php';

CBHTMLOutput::addCSSURL('https://fonts.googleapis.com/css?family=Source+Sans+Pro:600');
CBHTMLOutput::addCSSURL(CBSystemURL . '/handlers/handle,admin.css');

$spec                           = new stdClass();
$spec->selectedMenuItemName     = 'general';
$spec->selectedSubmenuItemName  = 'status';
CBAdminPageMenuView::renderModelAsHTML(CBAdminPageMenuView::specToModel($spec));

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

CBAdminPageFooterView::renderModelAsHTML();

CBHTMLOutput::render();
