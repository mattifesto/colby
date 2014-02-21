<?php

global $CBAdminMenu;
$CBAdminMenu = new stdClass();


$generalMenu            = new stdClass();
$generalMenu->status    = newMenuItem('Status', '/admin/');

$CBAdminMenu->general = newMenuItem('General', '/admin/', $generalMenu);


$pagesMenu                  = new stdClass();
$pagesMenu->edit            = newMenuItem('New Page', '/admin/pages/edit/');
$pagesMenu->unpublished     = newMenuItem('Unpublished', '/admin/pages/unpublished/');
$pagesMenu->search          = newMenuItem('Search', '/admin/pages/search/');
$pagesMenu->{'old-style'}   = newMenuitem('Old Style', '/admin/pages/old-style/');

$CBAdminMenu->pages = newMenuItem('Pages', '/admin/pages/unpublished/', $pagesMenu);


$helpMenu = new stdClass();
$helpMenu->{'markaround-syntax'}        = newMenuItem('Markaround',
                                                      '/admin/help/markaround-syntax/');
$helpMenu->{'title-subtitle'}           = newMenuItem('Titles &amp; Descriptions',
                                                      '/admin/help/title-subtitle/');
$helpMenu->{'caption-alternative-text'} = newMenuItem('Captions &amp; Alternative Text',
                                                      '/admin/help/caption-alternative-text');

$CBAdminMenu->help = newMenuItem('Help', '/admin/help/markaround-syntax/', $helpMenu);

if (ColbyUser::current()->isOneOfThe('Developers'))
{
    $generalMenu->permissions       = newMenuItem('Permissions', '/admin/users/');

    $developMenu                    = new stdClass();
    $developMenu->php               = newMenuItem('PHP', '/admin/develop/php/');
    $developMenu->{'test-pages'}    = newMenuItem('Test Pages', '/admin/develop/test-pages/');
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
