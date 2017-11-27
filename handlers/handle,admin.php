<?php

if (CBAdminPageForUpdate::installationIsRequired()) {
    CBAdminPageForUpdate::update();
}

if (!ColbyUser::current()->isOneOfThe('Administrators')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

$className = $_GET['c'] ?? '';
$pageStub = $_GET['p'] ?? '';

if (!empty($className)) {
    CBAdmin::render($className, $pageStub);
    return 1;
}

CBHTMLOutput::begin();
CBHTMLOutput::$classNameForSettings = 'CBPageSettingsForAdminPages';
CBHTMLOutput::setTitleHTML('Website Status');
CBHTMLOutput::setDescriptionHTML('The status of the website');
CBHTMLOutput::requireClassName('CBUI');
CBHTMLOutput::addCSSURL(CBSystemURL . '/handlers/handle,admin.css');

CBView::render((object)[
    'className' => 'CBAdminPageMenuView',
    'selectedMenuItemName' => 'general',
    'selectedSubmenuItemName' => 'status',
]);

?>

<main class="CBUIRoot">
    <div class="CBLibraryListView">
        <?php

        $statusWidgetFilepaths = Colby::globFiles('classes/CBStatusWidgetFor*');
        $statusWidgetClassNames = array_map(function ($filepath) {
            return basename($filepath, '.php');
        }, $statusWidgetFilepaths);

        sort($statusWidgetClassNames);

        array_walk($statusWidgetClassNames, function($className) {
            if (is_callable($function = "{$className}::CBStatusAdminPage_data")) {
                $data = call_user_func($function);

                ?>

                <section class="widget">
                    <header><h1><?= cbhtml($data[0]) ?></h1></header>

                    <div class="version-numbers">
                        <section class="version-number">
                            <h1><?= cbhtml($data[1]) ?></h1>
                            <div class="number"><?= cbhtml($data[2]) ?></div>
                        </section>
                    </div>
                </section>

                <?php
            }
        });

        /* deprecated: use status widget classes */
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

        /* deprecated: use status widget classes */
        $adminWidgetFilenames = Colby::globFiles('snippets/admin-widget-*.php');

        foreach ($adminWidgetFilenames as $adminWidgetFilename) {
            include $adminWidgetFilename;
        }

        ?>
    </div>

    <?php

    CBStatusAdminPage::renderDuplicatePublishedURIWarnings();
    CBStatusAdminPage::renderSiteConfigurationIssuesView();
    CBStatusAdminPage::renderPingStatus();

    ?>

</main>

<?php

CBView::render((object)[
    'className' => 'CBAdminPageFooterView',
]);

CBHTMLOutput::render();
