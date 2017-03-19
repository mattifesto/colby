<?php

include_once COLBY_SYSTEM_DIRECTORY . '/classes/ColbyNestedDictionaryBuilder.php';

define('CBPagesAdministrationDataStoreID', '5bda1825fe0be9524106061b910fd0b8e1dde0c2');

$menuBuilder = ColbyNestedDictionaryBuilder::builderWithTitle('Documents Administration Menu');

$menuBuilder->addValue('main', 'titleHTML', 'Overview');
$menuBuilder->addValue('main', 'uri', '/admin/documents/');

$menuBuilder->addValue('colby-documents-rows', 'titleHTML', 'ColbyPages Rows');
$menuBuilder->addValue('colby-documents-rows', 'uri', '/admin/documents/colby-pages-rows/');

$menuBuilder->addValue('stray-archives', 'titleHTML', 'Data Stores without Pages');
$menuBuilder->addValue('stray-archives', 'uri', '/admin/documents/data-stores-without-pages/');

$menuBuilder->addValue('stray-documents', 'titleHTML', 'Pages without Data Stores');
$menuBuilder->addValue('stray-documents', 'uri', '/admin/documents/pages-without-data-stores/');


global $documentsAdministrationMenu;

$documentsAdministrationMenu = $menuBuilder->nestedDictionary();

unset($menuBuilder);

/**
 * @return string
 */
function linkForArchiveId($archiveId) {
    $href = CBSitePreferences::siteURL() . "/admin/documents/view/?archive-id={$archiveId}";

    return "<a href=\"{$href}\"><span class=\"hash\">{$archiveId}</span></a>";
}

/**
 *
 */
function renderDocumentsAdministrationMenu() {
    global $documentsAdministrationMenu;

    echo '<ul class="horizontal">';

    foreach ($documentsAdministrationMenu->items as $itemKey => $item)
    {
        ?>

        <li class="item <?php echo $itemKey; ?>">
            <a href="<?php echo $item->uri; ?>">
                <?php echo $item->titleHTML; ?>
            </a>
        </li>

        <?php
    }

    echo '</ul>';
}

/**
 *
 */
function viewLinkForArchiveId($archiveId) {
    echo linkForArchiveId($archiveId);
}
