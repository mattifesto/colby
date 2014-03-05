<?php

include_once CBSystemDirectory . '/classes/ColbyDocument.php';
include_once CBSystemDirectory . '/snippets/shared/documents-administration.php';
include_once CBSystemDirectory . '/classes/CBHTMLOutput.php';


if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}


CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML('Documents');
CBHTMLOutput::setDescriptionHTML('List, view, delete, and manage archives.');

include CBSystemDirectory . '/sections/equalize.php';

CBHTMLOutput::addCSSURL('https://fonts.googleapis.com/css?family=Source+Sans+Pro:400');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/Colby.js');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/handlers/handle,admin,documents.js');


$selectedMenuItemID     = 'develop';
$selectedSubmenuItemID  = 'documents';

include CBSystemDirectory . '/sections/admin-page-menu.php';


$document = ColbyDocument::documentWithArchiveId(COLBY_DOCUMENTS_ADMINISTRATION_SHARED_ARCHIVE_ID);

$archive = $document->archive();

?>

<nav style="text-align: center; margin-bottom: 20px;">
    <?php renderDocumentsAdministrationMenu(); ?>
</nav>

<main style="font-family: 'Source Sans Pro';">

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
        <button onclick="ColbyArchivesExplorer.regenerateDocument();">Find Stray Archives and Documents</button>
    </div>
</main>

<?php

include CBSystemDirectory . '/sections/admin-page-footer-2.php';

CBHTMLOutput::render();

/* ---------------------------------------------------------------- */
