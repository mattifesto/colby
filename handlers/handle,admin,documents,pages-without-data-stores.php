<?php

if (!ColbyUser::current()->isOneOfThe('Developers')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

include_once CBSystemDirectory . '/snippets/shared/documents-administration.php';

CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/handlers/handle,admin,documents.js');

CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML('Stray Documents');
CBHTMLOutput::setDescriptionHTML('List, view, delete, and manage archives.');

include CBSystemDirectory . '/sections/admin-page-settings.php';

$spec                           = new stdClass();
$spec->selectedMenuItemName     = 'develop';
$spec->selectedSubmenuItemName  = 'documents';
CBAdminPageMenuView::renderModelAsHTML(CBAdminPageMenuView::specToModel($spec));

$dataStore  = new CBDataStore(CBPagesAdministrationDataStoreID);
$filepath   = $dataStore->directory() . '/data.json';

?>

<nav style="text-align: center; margin-bottom: 20px;">
    <?php renderDocumentsAdministrationMenu(); ?>
</nav>

<main>
    <h1>Pages without Data Stores</h1>

    <?php

    if (is_file($filepath)) {
        $data = json_decode(file_get_contents($filepath));

        ?>

        <section>

            <?php

            foreach ($data->pagesWithoutDataStores as $ID)
            {
                echo viewLinkForArchiveId($ID), ' ';
            }

            ?>

        </section>

        <?php
    }

    ?>

</main>

<?php

CBAdminPageFooterView::renderModelAsHTML();

CBHTMLOutput::render();
