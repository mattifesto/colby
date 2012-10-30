<?php

ColbyPage::requireVerifiedUser();

$args = new stdClass();
$args->title = 'Site Administration';
$args->description = 'Edit the settings and content of this website.';

ColbyPage::beginAdmin($args);

?>

<h1>Site Administration</h1>

<h2>Sections</h2>

<?php

$adminSections = glob(COLBY_SITE_DIRECTORY . '/colby/handlers/handle,admin~*.php');
$adminSections = $adminSections + glob(COLBY_SITE_DIRECTORY . '/handlers/handle,admin~*.php');

$adminSections = preg_grep('/handle,admin~[^~]*.php$/', $adminSections);

foreach ($adminSections as $adminSection)
{
    preg_match('/handle,admin~(.*).php$/', $adminSection, $matches);

    echo "<p><a href=\"/admin/{$matches[1]}/\">{$matches[1]}</a>\n";
}

ColbyPage::end();
