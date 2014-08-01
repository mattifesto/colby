<?php

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

include_once COLBY_SYSTEM_DIRECTORY . '/snippets/shared/documents-administration.php';

CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML('Stray Archives');
CBHTMLOutput::setDescriptionHTML('List, view, delete, and manage archives.');

include CBSystemDirectory . '/sections/admin-page-settings.php';

$menu = CBAdminPageMenuView::init();
$menu->setSelectedMenuItemName('develop');
$menu->setSelectedSubmenuItemName('documents');
$menu->renderHTML();

$document   = ColbyDocument::documentWithArchiveId(COLBY_DOCUMENTS_ADMINISTRATION_SHARED_ARCHIVE_ID);
$archive    = $document->archive();

?>

<nav style="text-align: center; margin-bottom: 20px;">
    <?php renderDocumentsAdministrationMenu(); ?>
</nav>

<main>
    <h1>Stray Archives</h1>

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

$footer = CBAdminPageFooterView::init();
$footer->renderHTML();

CBHTMLOutput::render();
