<?php

global $CBAdminMenu;
$CBAdminMenu = new stdClass();

$CBAdminMenu->home = newMenuItem('Home', '/');

$generalMenu            = new stdClass();
$generalMenu->status    = newMenuItem('Status', '/admin/');
$CBAdminMenu->general   = newMenuItem('General', '/admin/', $generalMenu);


$pagesMenu = new stdClass();
$pagesMenu->create = newMenuItem('Create', '/admin/pages/edit/');
$pagesMenu->find = newMenuItem('Find', '/admin/pages/find/');
$pagesMenu->trash = newMenuItem('Trash', '/admin/pages/trash/');
$CBAdminMenu->pages = newMenuItem('Pages', '/admin/pages/find/', $pagesMenu);

$CBAdminMenu->models = newMenuItem('Models', '/admin/models/directory/', (object)[
    'directory' => newMenuItem('Directory', '/admin/models/directory/'),
    'import' => newMenuItem('Import', '/admin/page/?class=CBAdminPageForModelImport')
]);

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
$CBAdminMenu->help          = newMenuItem('Help', '/admin/help/markaround-syntax/', $helpMenu);

if (ColbyUser::current()->isOneOfThe('Developers')) {
    $generalMenu->permissions       = newMenuItem('Permissions', '/admin/users/');

    $developMenu                    = new stdClass();
    $developMenu->images            = newMenuItem('Images', '/admin/develop/images/');
    $developMenu->php               = newMenuItem('PHP', '/admin/develop/php/');
    $developMenu->update            = newMenuItem('Update', '/developer/update/');
    $developMenu->documents         = newMenuItem('Pages', '/admin/documents/');
    $developMenu->mysql             = newMenuItem('MySQL', '/developer/mysql/');

    $CBAdminMenu->develop = newMenuItem('Develop', '/admin/develop/php/', $developMenu);

    $testMenu                           = new stdClass();
    $testMenu->test                     = newMenuItem('Unit Tests', '/developer/test/');

    $CBAdminMenu->test = newMenuItem('Test', '/developer/test/', $testMenu);
}

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
