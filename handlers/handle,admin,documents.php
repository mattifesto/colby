<?php

include_once COLBY_SYSTEM_DIRECTORY . '/classes/ColbyDocument.php';
include_once COLBY_SYSTEM_DIRECTORY . '/snippets/shared/documents-administration.php';
include_once CBSystemDirectory . '/classes/CBHTMLOutput.php';


CBHTMLOutput::setTitleHTML('Documents');
CBHTMLOutput::setDescriptionHTML('List, view, delete, and manage archives.');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/handlers/handle,admin,develop,test-pages.js');
CBHTMLOutput::begin();


if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    include Colby::findSnippet('authenticate.php');

    goto done;
}


include CBSystemDirectory . '/sections/admin-page-header.php';

$selectedMenuItemID     = 'develop';
$selectedSubmenuItemID  = 'documents';

include CBSystemDirectory . '/sections/admin-page-menu.php';


$document = ColbyDocument::documentWithArchiveId(COLBY_DOCUMENTS_ADMINISTRATION_SHARED_ARCHIVE_ID);

$archive = $document->archive();

?>

<nav style="text-align: center; margin-bottom: 20px;">
    <?php renderDocumentsAdministrationMenu(); ?>
</nav>

<main>
    <h1>Documents Administration</h1>

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

include CBSystemDirectory . '/sections/admin-page-footer.php';

done:

CBHTMLOutput::render();

/* ---------------------------------------------------------------- */
