<?php

global $CBAdminMenu;
$CBAdminMenu = new stdClass();

$CBAdminMenu->home = newMenuItem('Home', '/');

$generalMenu            = new stdClass();
$generalMenu->status = newMenuItem('Status', '/admin/');
$generalMenu->tasks = newMenuItem('Tasks', '/admin/page/?class=CBAdminPageForTasks');
$generalMenu->logs = newMenuItem('Logs', '/admin/page/?class=CBAdminPageForLogs');
$generalMenu->users = newMenuItem('Users', '/admin/page/?class=CBAdminPageForUsers');
$CBAdminMenu->general = newMenuItem('General', '/admin/', $generalMenu);


$pagesMenu = new stdClass();
$pagesMenu->create = newMenuItem('Create', '/admin/pages/edit/');
$pagesMenu->find = newMenuItem('Find', '/admin/page/?class=CBAdminPageForPagesFind');
$pagesMenu->trash = newMenuItem('Trash', '/admin/page?class=CBAdminPageForPagesTrash');
$CBAdminMenu->pages = newMenuItem('Pages', '/admin/page/?class=CBAdminPageForPagesFind', $pagesMenu);


$modelsMenu = (object)[
    'directory' => newMenuItem('Directory', '/admin/models/directory/'),
    'import' => newMenuItem('Import', '/admin/page/?class=CBAdminPageForModelImport'),
];

$helpMenu                   = new stdClass();
$menuItemID                 = 'markaround-syntax';
$menuItemHTML               = 'Markaround';
$menuItemURI                = '/admin/help/markaround-syntax/';
$helpMenu->{$menuItemID}    = newMenuItem($menuItemHTML, $menuItemURI);
$menuItemID                 = 'title-subtitle';
$menuItemHTML               = 'Titles &amp; Descriptions';
$menuItemURI                = '/admin/help/title-subtitle/';
$helpMenu->{$menuItemID}    = newMenuItem($menuItemHTML, $menuItemURI);
$menuItemID                 = 'caption-alternative-text';
$menuItemHTML               = 'Captions &amp; Alternative Text';
$menuItemURI                = '/admin/help/caption-alternative-text';
$helpMenu->{$menuItemID}    = newMenuItem($menuItemHTML, $menuItemURI);
$helpMenu->CBArtworkElement = newMenuItem('CBArtworkElement', '/admin/page/?class=CBAdminPageForCBArtworkElement');
$CBAdminMenu->help          = newMenuItem('Help', '/admin/help/markaround-syntax/', $helpMenu);

if (ColbyUser::current()->isOneOfThe('Developers')) {
    $developMenu                    = new stdClass();
    $developMenu->images            = newMenuItem('Images', '/admin/page/?class=CBAdminPageForImages');
    $developMenu->php               = newMenuItem('PHP', '/admin/develop/php/');
    $developMenu->update            = newMenuItem('Update', '/admin/page/?class=CBAdminPageForUpdate');
    $developMenu->documents         = newMenuItem('Pages', '/admin/documents/');
    $developMenu->mysql             = newMenuItem('MySQL', '/developer/mysql/');

    $CBAdminMenu->develop = newMenuItem('Develop', '/admin/develop/php/', $developMenu);

    $testMenu = (object)[
        'test' => newMenuItem('Website Tests', '/admin/page/?class=CBAdminPageForTests'),
    ];

    $CBAdminMenu->test = newMenuItem('Test', '/admin/page/?class=CBAdminPageForTests', $testMenu);

    $modelsMenu->inspector = newMenuItem('Inspector', '/admin/page/?class=CBAdminPageForModelInspector');
}

$CBAdminMenu->models = newMenuItem('Models', '/admin/models/directory/', $modelsMenu);

/**
 * @return stdClass
 */
function newMenuItem($nameHTML, $URI, $submenu = null)
{
    $menuItem           = new stdClass();
    $menuItem->nameHTML = $nameHTML;
    $menuItem->URI      = $URI;
    $menuItem->submenu  = $submenu;

    return $menuItem;
}
