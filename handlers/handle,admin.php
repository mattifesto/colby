<?php

$page = ColbyOutputManager::beginVerifiedUserPage('Site Administration',
                                                  'Edit the settings and content of this website.',
                                                  'admin');

?>

<h1>Content</h1>

<?php

$adminSections = glob(COLBY_SITE_DIRECTORY . '/colby/handlers/handle,admin,*.php');
$adminSections = array_merge($adminSections, glob(COLBY_SITE_DIRECTORY . '/handlers/handle,admin,*.php'));

$adminSections = preg_grep('/handle,admin,[^,]*.php$/', $adminSections);

foreach ($adminSections as $adminSection)
{
    preg_match('/handle,admin,(.*).php$/', $adminSection, $matches);

    echo "<p><a href=\"/admin/{$matches[1]}/\">{$matches[1]}</a>\n";
}

?>

<h1>Help</h1>

<?php

$helpPages = glob(COLBY_SITE_DIRECTORY . '/colby/handlers/handle,admin,help,*.php');
$helpPages = array_merge($helpPages, glob(COLBY_SITE_DIRECTORY . '/handlers/handle,admin,help,*.php'));

$helpPages = preg_grep('/handle,admin,help,[^,]*.php$/', $helpPages);

foreach ($helpPages as $helpPage)
{
    preg_match('/handle,admin,help,(.*).php$/', $helpPage, $matches);

    $helpPageStub = $matches[1];

    echo "<p><a href=\"/admin/help/{$helpPageStub}/\">{$helpPageStub}</a>\n";
}

$page->end();
