<?php

include_once COLBY_SYSTEM_DIRECTORY . '/classes/ColbyDocument.php';
include_once COLBY_SYSTEM_DIRECTORY . '/snippets/shared/documents-administration.php';

$page = new ColbyOutputManager('admin-html-page');

$page->titleHTML = 'Stray Archives';
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
    <h1>Stray Archives</h1>

    <nav style="text-align: center;">
        <?php renderDocumentsAdministrationMenu(); ?>
    </nav>

    <?php

    $strayArchiveIds = $archive->valueForKey('strayArchiveIds');

    if ($strayArchiveIds && $strayArchiveIds->count() > 0)
    {
        ?>

        <section>

            <?php

            foreach ($strayArchiveIds as $strayArchiveId)
            {
                echo viewLinkForArchiveId($strayArchiveId), ' ';
            }

            ?>

        </section>

        <?php
    }

    ?>

</main>

<?php

done:

$page->end();

/* ---------------------------------------------------------------- */
