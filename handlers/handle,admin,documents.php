<?php

if (!ColbyUser::current()->isOneOfThe('Developers')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

include_once CBSystemDirectory . '/snippets/shared/documents-administration.php';

CBHTMLOutput::$classNameForSettings = 'CBPageSettingsForAdminPages';
CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML('Documents');
CBHTMLOutput::setDescriptionHTML('List, view, delete, and manage archives.');
CBHTMLOutput::requireClassName('CBUI');
CBHTMLOutput::requireClassName('CBDataStoreAdmin');

CBView::render((object)[
    'className' => 'CBAdminPageMenuView',
    'selectedMenuItemName' => 'develop',
    'selectedSubmenuItemName' => 'documents',
]);

$filepath = CBDataStore::flexpath(CBPagesAdministrationDataStoreID, 'data.json', cbsitedir());

if (is_file($filepath)) {
    $data = json_decode(file_get_contents($filepath));
}

?>

<nav style="text-align: center; margin-bottom: 20px;">
    <?php renderDocumentsAdministrationMenu(); ?>
</nav>

<main class="CBUIRoot">

    <?php

    if (isset($data)) {
        $countOfDataStoresWithoutPages = count($data->dataStoresWithoutPages);
        $countOfPagesWithoutDataStores = count($data->pagesWithoutDataStores);
    } else {
        $countOfDataStoresWithoutPages = 'Unknown';
        $countOfPagesWithoutDataStores = 'Unknown';
    }

    ?>

    <ul class="horizontal" style="text-align: center;">
        <li>Data Stores without Pages: <?= $countOfDataStoresWithoutPages ?></li>
        <li>Pages without Data Stores: <?= $countOfPagesWithoutDataStores ?></li>
    </ul>

    <div style="text-align: center;">
        <progress value="0" max="256" id="progress" style="margin-bottom: 20px;"></progress><br>
        <button onclick="CBDataStoreAdmin.regenerateDocument();">Find Stray Archives and Documents</button>
    </div>
</main>

<?php

CBView::render((object)[
    'className' => 'CBAdminPageFooterView',
]);

CBHTMLOutput::render();
