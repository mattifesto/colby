<?php

include(Colby::findHandler('handle-ensure-installation.php'));

if (!ColbyUser::current()->isOneOfThe('Administrators')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

CBHTMLOutput::begin();
CBHTMLOutput::$classNameForSettings = 'CBPageSettingsForAdminPages';
CBHTMLOutput::setTitleHTML('Website Status');
CBHTMLOutput::setDescriptionHTML('The status of the website');
CBHTMLOutput::requireClassName('CBUI');
CBHTMLOutput::addCSSURL(CBSystemURL . '/handlers/handle,admin.css');

$spec                           = new stdClass();
$spec->selectedMenuItemName     = 'general';
$spec->selectedSubmenuItemName  = 'status';
CBAdminPageMenuView::renderModelAsHTML(CBAdminPageMenuView::specToModel($spec));

?>

<main class="CBUIRoot">
    <div class="CBLibraryListView">
        <?php

        $widgetClassNames = Colby::globFiles('classes/*AdminWidgetFor*');
        $widgetClassNames = array_map(function ($className) {
            return basename($className, '.php');
        }, $widgetClassNames);

        sort($widgetClassNames);

        array_walk($widgetClassNames, function($className) {
            if (is_callable($function = "{$className}::render")) {
                call_user_func($function);
            }
        });

        /* deprecated: use widget classes */
        $adminWidgetFilenames = Colby::globFiles('snippets/admin-widget-*.php');

        foreach ($adminWidgetFilenames as $adminWidgetFilename) {
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
