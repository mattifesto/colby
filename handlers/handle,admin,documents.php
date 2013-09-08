<?php

include_once COLBY_SYSTEM_DIRECTORY . '/classes/ColbyDocument.php';
include_once COLBY_SYSTEM_DIRECTORY . '/snippets/shared/documents-administration.php';

$page = new ColbyOutputManager('admin-html-page');

$page->titleHTML = 'Archives';
$page->descriptionHTML = 'List, view, delete, and manage archives.';

$page->begin();

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    include Colby::findSnippet('authenticate.php');

    goto done;
}

$document = ColbyDocument::documentWithArchiveId(COLBY_DOCUMENTS_ADMINISTRATION_SHARED_ARCHIVE_ID);

$archive = $document->archive();

?>

<main>
    <h1>Archives</h1>

    <nav style="text-align: center;">
        <?php renderDocumentsAdministrationMenu(); ?>
    </nav>

    <?php

    $strayArchiveIds = $archive->valueForKey('strayArchiveIds');

    if ($strayArchiveIds)
    {
        $countOfStrayArchives = $strayArchiveIds->count();
    }
    else
    {
        $countOfStrayArchives = 'unknown';
    }

    $strayDocumentArchiveIds = $archive->valueForKey('strayDocumentArchiveIds');

    if ($strayDocumentArchiveIds)
    {
        $countOfStrayDocuments = $strayDocumentArchiveIds->count();
    }
    else
    {
        $countOfStrayDocuments = 'unknown';
    }

    ?>

    <ul class="horizontal" style="text-align: center;">
        <li>Stray Archives: <?php echo $countOfStrayArchives; ?></li>
        <li>Stray Documents: <?php echo $countOfStrayDocuments; ?></li>
    </ul>

    <div style="text-align: center;">
        <progress value="0" max="256" id="progress" style="margin-bottom: 20px;"></progress><br>
        <a class="big-button" onclick="ColbyArchivesExplorer.regenerateDocument();">Find Stray Archives and Documents</a>
    </div>
</main>

<script src="<?php echo COLBY_SYSTEM_URL . '/handlers/handle,admin,documents.js'; ?>"></script>

<?php

done:

$page->end();

/* ---------------------------------------------------------------- */
