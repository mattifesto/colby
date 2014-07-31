<?php

global $CBAdminMenu;
$CBAdminMenu = new stdClass();

$CBAdminMenu->home = newMenuItem('Home', '/');

$generalMenu            = new stdClass();
$generalMenu->status    = newMenuItem('Status', '/admin/');
$CBAdminMenu->general   = newMenuItem('General', '/admin/', $generalMenu);


$pagesMenu                  = new stdClass();
$pagesMenu->edit            = newMenuItem('New Page', '/admin/pages/edit/');
$pagesMenu->unpublished     = newMenuItem('Unpublished', '/admin/pages/unpublished/');
$pagesMenu->search          = newMenuItem('Search', '/admin/pages/search/');
$pagesMenu->trash           = newMenuItem('Trash', '/admin/pages/trash/');
$pagesMenu->{'old-style'}   = newMenuitem('Old Style', '/admin/pages/old-style/');
$CBAdminMenu->pages         = newMenuItem('Pages', '/admin/pages/unpublished/', $pagesMenu);


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

if (ColbyUser::current()->isOneOfThe('Developers'))
{
    $generalMenu->permissions       = newMenuItem('Permissions', '/admin/users/');

    $developMenu                    = new stdClass();
    $developMenu->menus             = newMenuItem('Menus', '/admin/develop/menus/');
    $developMenu->php               = newMenuItem('PHP', '/admin/develop/php/');
    $developMenu->update            = newMenuItem('Update', '/developer/update/');
    $developMenu->documents         = newMenuItem('Documents', '/admin/documents/');
    $developMenu->groups            = newMenuItem('Groups', '/developer/groups/');
    $developMenu->model             = newMenuItem('Types', '/developer/models/');
    $developMenu->mysql             = newMenuItem('MySQL', '/developer/mysql/');

    $CBAdminMenu->develop = newMenuItem('Develop', '/admin/develop/php/', $developMenu);

    $testMenu                           = new stdClass();
    $testMenu->test                     = newMenuItem('Unit Tests', '/developer/test/');
    $testMenu->{'performance-tests'}    = newMenuItem('MySQL vs. ColbyArchive', '/developer/performance-tests/mysql-vs-colbyarchive/');

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
