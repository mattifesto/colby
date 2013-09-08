<?php

include_once COLBY_SYSTEM_DIRECTORY . '/classes/ColbyDocument.php';
include_once COLBY_SYSTEM_DIRECTORY . '/snippets/shared/documents-administration.php';

$page = new ColbyOutputManager('admin-html-page');

$page->titleHTML = 'Stray Documents';
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
    <h1>Stray Documents</h1>

    <nav style="text-align: center;">
        <?php renderDocumentsAdministrationMenu(); ?>
    </nav>

    <?php

    $strayDocumentArchiveIds = $archive->valueForKey('strayDocumentArchiveIds');

    if ($strayDocumentArchiveIds && $strayDocumentArchiveIds->count() > 0)
    {
        ?>

        <section>

            <?php

            foreach ($strayDocumentArchiveIds as $strayDocumentArchiveId)
            {
                echo viewLinkForArchiveId($strayDocumentArchiveId), ' ';
            }

            ?>

        </section>

        <?php
    }

    ?>

</main>

<script src="<?php echo COLBY_SYSTEM_URL . '/handlers/handle,admin,documents.js'; ?>"></script>

<?php

done:

$page->end();

/* ---------------------------------------------------------------- */
