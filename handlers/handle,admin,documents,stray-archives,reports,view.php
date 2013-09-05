<?php

include_once COLBY_DIRECTORY . '/classes/ColbyDocument.php';
include_once COLBY_DIRECTORY . '/snippets/shared/documents-administration.php';

$page = new ColbyOutputManager('admin-html-page');

$page->titleHTML = 'Stray Archive Report';
$page->descriptionHTML = 'View a stray archive report.';

$page->begin();

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    include Colby::findSnippet('authenticate.php');

    goto done;
}

$reportId = $_GET['report-id'];

$archive = ColbyArchive::open(COLBY_DOCUMENTS_ADMINISTRATION_SHARED_ARCHIVE_ID);

$reports = $archive->valueForKey('reports');
$report = null;

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

    <nav style="text-align: center;">
        <?php renderDocumentsAdministrationMenu(); ?>
    </nav>

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

</main>

<?php

done:

$page->end();

