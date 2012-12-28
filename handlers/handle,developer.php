<?php

$page = ColbyOutputManager::beginVerifiedUserPage('Developer Tools',
                                                  'Tools to help build a website.',
                                                  'admin');

?>

<h1>Developer Tools</h1>

<?php

$developerSections = glob(COLBY_SITE_DIRECTORY . '/colby/handlers/handle,developer,*.php');
$developerSections = array_merge($developerSections, glob(COLBY_SITE_DIRECTORY . '/handlers/handle,developer,*.php'));

// Reduce list to only files matching the exact pattern of developer admin section pages.

$developerSections = preg_grep('/handle,developer,[^,]*.php$/', $developerSections);

foreach ($developerSections as $developerSection)
{
    preg_match('/handle,developer,(.*).php$/', $developerSection, $matches);

    echo "<p><a href=\"/developer/{$matches[1]}/\">{$matches[1]}</a>\n";
}

$page->end();
