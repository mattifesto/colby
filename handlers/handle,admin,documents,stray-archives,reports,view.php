<?php

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

include_once COLBY_SYSTEM_DIRECTORY . '/snippets/shared/documents-administration.php';

CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML('Stray Archive Report');
CBHTMLOutput::setDescriptionHTML('View a stray archive report.');

include CBSystemDirectory . '/sections/admin-page-settings.php';

CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/handlers/handle,admin,documents,stray-archives,reports,view.js');

$menu = CBAdminPageMenuView::init();
$menu->setSelectedMenuItemName('develop');
$menu->setSelectedSubmenuItemName('documents');
$menu->renderHTML();

$reportId   = $_GET['report-id'];
$archive    = ColbyArchive::open(COLBY_DOCUMENTS_ADMINISTRATION_SHARED_ARCHIVE_ID);
$reports    = $archive->valueForKey('reports');
$report     = null;

if ($reports && isset($reports->items->{$reportId}))
{
    $report = $reports->items->{$reportId};
    $title = "Report: {$report->name}";
}
else
{
    $title = 'Report not found.';
}

?>

<nav style="text-align: center; margin-bottom: 20px;">
    <?php renderDocumentsAdministrationMenu(); ?>
</nav>

<main>
    <style scoped>

        section
        {
            margin-top: 50px;
        }

        dl
        {
            margin: 20px 50px;
        }

        dt
        {
            font-weight: bold;
        }

        dd
        {
            margin: 5px 40px 0px;
        }

        dd + dt
        {
            margin-top: 10px;
        }

    </style>

    <h1><?php echo $title; ?></h1>

    <?php

    if ($report)
    {
        ?>

        <section>

            <h1>Report Parameters</h1>

            <dl>
                <dt>Query Field Name</dt>
                <dd><?php echo ColbyConvert::textToHTML($report->queryFieldName); ?></dd>
                <dt>Query Field Value</dt>
                <dd><?php echo ColbyConvert::textToHTML($report->queryFieldValue); ?></dd>
                <dt>Count of Matching Archives</dt>
                <dd><?php echo $report->resultArchiveIds->count(); ?></dd>
            </dl>
        </section>

        <section>
            <h1>Archives</h1>

            <div>

                <?php

                foreach ($report->resultArchiveIds as $strayArchiveId)
                {
                    echo viewLinkForArchiveId($strayArchiveId), "\n";
                }

                ?>

            </div>
        </section>

        <?php
    }

    ?>

    <div style="text-align: center;">
        <progress value="0" id="progress" style="margin-bottom: 20px;"></progress><br>
        <a class="big-button" onclick="ColbyReportArchiveDeleter.deleteArchives();">Delete the stray archives in this report</a>
    </div>
</main>

<input type="hidden" id="report-id" value="<?php echo $reportId; ?>">

<?php

$footer = CBAdminPageFooterView::init();
$footer->renderHTML();

CBHTMLOutput::render();

