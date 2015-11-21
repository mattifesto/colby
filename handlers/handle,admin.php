<?php

include(Colby::findHandler('handle-ensure-installation.php'));

if (!ColbyUser::current()->isOneOfThe('Administrators')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

CBHTMLOutput::begin();
CBHTMLOutput::$classNameForSettings = 'CBPageSettingsForAdminPages';
CBHTMLOutput::setTitleHTML('Website Status');
CBHTMLOutput::setDescriptionHTML('The status of the website');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/CBUI.js');
CBHTMLOutput::addCSSURL(CBSystemURL . '/handlers/handle,admin.css');

$spec                           = new stdClass();
$spec->selectedMenuItemName     = 'general';
$spec->selectedSubmenuItemName  = 'status';
CBAdminPageMenuView::renderModelAsHTML(CBAdminPageMenuView::specToModel($spec));

?>

<main class="CBUIRoot">
    <div class="CBLibraryListView CBSystemFont">
        <?php

        $adminWidgetFilenames = Colby::globSnippets('admin-widget-*.php');

        foreach ($adminWidgetFilenames as $adminWidgetFilename)
        {
            include $adminWidgetFilename;
        }

        ?>
    </div>

    <?php

    CBAdminPageForGeneral::renderDuplicatePublishedURIWarnings();

    CBAdminPageForGeneral::renderSiteConfigurationIssuesView();

    ?>

</main>

<?php

CBAdminPageFooterView::renderModelAsHTML();

CBHTMLOutput::render();
