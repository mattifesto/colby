<?php

define('CBPagesAdministrationDataStoreID', '5bda1825fe0be9524106061b910fd0b8e1dde0c2');

/**
 * @return string
 */
function linkForArchiveId($archiveId) {
    $href = CBSitePreferences::siteURL() . "/admin/documents/view/?archive-id={$archiveId}";

    return "<a href=\"{$href}\"><span class=\"hash\">{$archiveId}</span></a>";
}

/**
 * @param string $selectedItemName
 *
 * @return null
 */
function renderDocumentsAdministrationMenu($selectedItemName = '') {
    $menu = (object)[
        'className' => 'CBMenu',
        'title' => 'Documents',
        'titleURI' => '/admin/documents/',
        'items' => [
            (object)[
                'className' => 'CBMenuItem',
                'name' => 'overview',
                'text' => 'Overview',
                'URL' => '/admin/documents/'
            ],
            (object)[
                'className' => 'CBMenuItem',
                'name' => 'colbypages',
                'text' => 'ColbyPages',
                'URL' => '/admin/documents/colby-pages-rows/'
            ],
            (object)[
                'className' => 'CBMenuItem',
                'name' => 'datastoreswithoutpages',
                'text' => 'Data Stores w/o Pages',
                'URL' => '/admin/documents/data-stores-without-pages/'
            ],
            (object)[
                'className' => 'CBMenuItem',
                'name' => 'pageswithoutdatastores',
                'text' => 'Pages w/o Data Stores',
                'URL' => '/admin/documents/pages-without-data-stores/'
            ],
        ],
    ];

    CBView::render((object)[
        'className' => 'CBMenuView',
        'CSSClassNames' => ['submenu1'],
        'menu' => $menu,
        'selectedItemName' => $selectedItemName,
    ]);
}

/**
 *
 */
function viewLinkForArchiveId($archiveId) {
    echo linkForArchiveId($archiveId);
}
