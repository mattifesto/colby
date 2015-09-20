<?php

include(Colby::findHandler('handle-ensure-installation.php'));

if (!ColbyUser::current()->isOneOfThe('Administrators')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML('Status');
CBHTMLOutput::setDescriptionHTML('The status of the website');

include CBSystemDirectory . '/sections/admin-page-settings.php';

CBHTMLOutput::addCSSURL(CBSystemURL . '/handlers/handle,admin.css');

$spec                           = new stdClass();
$spec->selectedMenuItemName     = 'general';
$spec->selectedSubmenuItemName  = 'status';
CBAdminPageMenuView::renderModelAsHTML(CBAdminPageMenuView::specToModel($spec));

?>

<div class="CBLibraryListView">
    <?php

    $adminWidgetFilenames = Colby::globSnippets('admin-widget-*.php');

    foreach ($adminWidgetFilenames as $adminWidgetFilename)
    {
        include $adminWidgetFilename;
    }

    ?>
</div>

<?php

CBAdminPageForGeneral::renderSiteConfigurationIssuesView();

CBAdminPageFooterView::renderModelAsHTML();

CBHTMLOutput::render();
