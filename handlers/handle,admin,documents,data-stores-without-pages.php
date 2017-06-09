<?php

if (!ColbyUser::current()->isOneOfThe('Developers')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

include_once COLBY_SYSTEM_DIRECTORY . '/snippets/shared/documents-administration.php';

CBHTMLOutput::$classNameForSettings = 'CBPageSettingsForAdminPages';
CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML('Stray Archives');
CBHTMLOutput::setDescriptionHTML('List, view, delete, and manage archives.');

CBView::renderModelAsHTML((object)[
    'className' => 'CBAdminPageMenuView',
    'selectedMenuItemName' => 'develop',
    'selectedSubmenuItemName' => 'documents',
]);

$filepath = CBDataStore::directoryForID(CBPagesAdministrationDataStoreID) . '/data.json';

?>

<nav style="text-align: center; margin-bottom: 20px;">
    <?php renderDocumentsAdministrationMenu(); ?>
</nav>

<main>
    <h1>Data Stores without Pages</h1>

    <?php

    if (is_file($filepath)) {
        $data = json_decode(file_get_contents($filepath));

        ?>

        <section>

            <?php

            foreach ($data->dataStoresWithoutPages as $ID)
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
