<?php

include_once COLBY_DIRECTORY . '/classes/ColbyDocument.php';
include_once COLBY_DIRECTORY . '/snippets/shared/documents-administration.php';

$page = new ColbyOutputManager('admin-html-page');

$page->titleHTML = 'Archives';
$page->descriptionHTML = 'List, view, delete, and manage archives.';

$page->begin();

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    include Colby::findSnippet('authenticate.php');

    goto done;
}

$archive = ColbyArchive::open(COLBY_DOCUMENTS_ADMINISTRATION_SHARED_ARCHIVE_ID);

?>

<main>
    <h1>Query Stray Archives</h1>

    <nav style="text-align: center;">
        <?php renderDocumentsAdministrationMenu(); ?>
    </nav>

    <?php

    $reports = $archive->valueForKey('reports');

    if ($reports)
    {
        echo '<nav style="text-align: center; font-size: 90%;"><p>Reports<ul class="horizontal">';

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

        echo '</ul></nav>';
    }


    ?>

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

<script src="<?php echo COLBY_URL; ?>/handlers/handle,admin,documents,stray-archives,query.js"></script>

<?php

done:

$page->end();

