<?php

global $CBAdminMenu;
$CBAdminMenu = new stdClass();

$CBAdminMenu->home = newMenuItem('Home', '/');

$generalMenu            = new stdClass();
$generalMenu->status = newMenuItem('Status', '/admin/');
$generalMenu->tasks = newMenuItem('Tasks', '/admin/page/?class=CBTasks2AdminPage');
$generalMenu->logs = newMenuItem('Logs', '/admin/page/?class=CBLogAdminPage');
$generalMenu->users = newMenuItem('Users', '/admin/page/?class=CBAdminPageForUsers');
$CBAdminMenu->general = newMenuItem('General', '/admin/', $generalMenu);

/* Pages */

$pagesMenu = new stdClass();
$pagesMenu->create = newMenuItem('Create', '/admin/pages/edit/');
$pagesMenu->find = newMenuItem('Find', '/admin/page/?class=CBAdminPageForPagesFind');
$pagesMenu->trash = newMenuItem('Trash', '/admin/page?class=CBAdminPageForPagesTrash');

if (ColbyUser::current()->isOneOfThe('Developers')) {
    $pagesMenu->develop = newMenuItem('Develop', '/admin/page/?class=CBPagesDevelopmentAdminPage');
}

$CBAdminMenu->pages = newMenuItem('Pages', '/admin/page/?class=CBAdminPageForPagesFind', $pagesMenu);

/* Models */

$modelsMenu = (object)[
    'directory' => newMenuItem('Directory', '/admin/models/directory/'),
    'import' => newMenuItem('Import', '/admin/page/?class=CBAdminPageForModelImport'),
];

$helpMenu = (object)[
    'markaround-syntax' => (object)[
        'nameHTML' => 'Markaround',
        'URI' => '/admin/help/markaround-syntax/'
    ],
    'title-subtitle' => (object)[
        'nameHTML' => 'Titles &amp; Descriptions',
        'URI' => '/admin/help/title-subtitle/',
    ],
    'caption-alternative-text' => (object)[
        'nameHTML' => 'Captions &amp; Alternative Text',
        'URI' => '/admin/help/caption-alternative-text',
    ],
    'CBArtworkElement' => (object)[
        'nameHTML' => 'CBArtworkElement',
        'URI' => '/admin/page/?class=CBAdminPageForCBArtworkElement',
    ],
    'api' => (object)[
        'nameHTML' => 'API',
        'URI' => '/admin/help/api/',
    ],
    'cssvariables' => (object)[
        'nameHTML' => 'CSS Variables',
        'URI' => '/admin/page/?class=CBAdminPageForCSSVariables'
    ],
];

$CBAdminMenu->help = newMenuItem('Help', '/admin/help/markaround-syntax/', $helpMenu);

if (ColbyUser::current()->isOneOfThe('Developers')) {
    $developMenu                = new stdClass();
    $developMenu->datastores    = newMenuItem('Data Stores', '/admin/page/?class=CBDataStoresAdminPage');
    $developMenu->images        = newMenuItem('Images', '/admin/page/?class=CBImagesAdminPage');
    $developMenu->php           = newMenuItem('PHP', '/admin/develop/php/');
    $developMenu->update        = newMenuItem('Update', '/admin/page/?class=CBAdminPageForUpdate');
    $developMenu->mysql         = newMenuItem('MySQL', '/developer/mysql/');

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
