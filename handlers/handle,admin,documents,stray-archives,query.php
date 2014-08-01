<?php

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

include_once COLBY_SYSTEM_DIRECTORY . '/snippets/shared/documents-administration.php';

CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML('Archives');
CBHTMLOutput::setDescriptionHTML('List, view, delete, and manage archives.');

include CBSystemDirectory . '/sections/admin-page-settings.php';

CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/handlers/handle,admin,documents,stray-archives,query.js');

$menu = CBAdminPageMenuView::init();
$menu->setSelectedMenuItemName('develop');
$menu->setSelectedSubmenuItemName('documents');
$menu->renderHTML();

$archive = ColbyArchive::open(COLBY_DOCUMENTS_ADMINISTRATION_SHARED_ARCHIVE_ID);

?>

<nav style="text-align: center; margin-bottom: 20px;">

    <?php

    renderDocumentsAdministrationMenu();

    $reports = $archive->valueForKey('reports');

    if ($reports)
    {
        ?>

        <div style="margin: 10px 0px 5px; color: #7f7f7f; font-family: Georgia, serif; font-size: 0.7em; font-style: italic;">~ reports ~</div>

        <ul class="horizontal" style="font-size: 0.9em;">

        <?php

        foreach ($reports->items as $reportId => $report)
        {
            $reportURL = COLBY_SITE_URL .
                         '/admin/documents/stray-archives/reports/view/?' .
                         "report-id={$reportId}";
            ?>

            <li>
                <a href="<?php echo $reportURL; ?>">
                    <?php echo $report->name; ?>
                </a>
            </li>

            <?php
        }

        ?>

        </ul>

        <?php
    }

    ?>

</nav>

<main>
    <h1>Query Stray Archives</h1>

    <div>
        <div>
            <label>Report Name: <input type="text" id="report-name"></label>
        </div>
        <div>
            <label>Field Name: <input type="text" id="query-field-name"></label>
        </div>
        <div>
            <label>Field Value: <input type="text" id="query-field-value"></label>
        </div>
        <div style="text-align: center;">
            <progress value="0" max="256" id="progress" style="margin-bottom: 20px;"></progress><br>
            <a class="big-button" onclick="ColbyStrayArchivesFinder.runQuery();">Query Stray Archives</a>
        </div>
    </div>
</main>

<?php

$footer = CBAdminPageFooterView::init();
$footer->renderHTML();

CBHTMLOutput::render();
