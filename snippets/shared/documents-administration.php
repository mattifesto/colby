<?php

include_once COLBY_DIRECTORY . '/classes/ColbyNestedDictionaryBuilder.php';

define('COLBY_DOCUMENTS_ADMINISTRATION_SHARED_ARCHIVE_ID', '5bda1825fe0be9524106061b910fd0b8e1dde0c2');

$menuBuilder = ColbyNestedDictionaryBuilder::builderWithTitle('Documents Administration Menu');

$menuBuilder->addValue('main', 'titleHTML', 'Main');
$menuBuilder->addValue('main', 'uri', '/admin/documents/');

$menuBuilder->addValue('stray-archives', 'titleHTML', 'Stray Archives');
$menuBuilder->addValue('stray-archives', 'uri', '/admin/documents/stray-archives/');

$menuBuilder->addValue('stray-documents', 'titleHTML', 'Stray Documents');
$menuBuilder->addValue('stray-documents', 'uri', '/admin/documents/stray-documents/');

$menuBuilder->addValue('query-stray-archives', 'titleHTML', 'Query Stray Archives');
$menuBuilder->addValue('query-stray-archives', 'uri', '/admin/documents/stray-archives/query/');

global $documentsAdministrationMenu;

$documentsAdministrationMenu = $menuBuilder->nestedDictionary();

unset($menuBuilder);

/**
 *
 */
function renderDocumentsAdministrationMenu()
{
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
