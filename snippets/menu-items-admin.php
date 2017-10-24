<?php

global $CBAdminMenu;
$CBAdminMenu = new stdClass();

$CBAdminMenu->home = newMenuItem('Home', '/');

$generalMenu = [
    'status' => (object)[
        'className' => 'CBMenuItem',
        'name' => 'status',
        'text' => 'Status',
        'URL' => '/admin/',
    ],
    'tasks' => (object)[
        'className' => 'CBMenuItem',
        'name' => 'tasks',
        'text' => 'Tasks',
        'URL' => '/admin/page/?class=CBTasks2AdminPage',
    ],
    'logs' => (object)[
        'className' => 'CBMenuItem',
        'name' => 'logs',
        'text' => 'Logs',
        'URL' => '/admin/page/?class=CBLogAdminPage',
    ],
    'users' => (object)[
        'className' => 'CBMenuItem',
        'name' => 'users',
        'text' => 'Users',
        'URL' => '/admin/page/?class=CBAdminPageForUsers',
    ],
];

$CBAdminMenu->general = newMenuItem('General', '/admin/', $generalMenu);

/* Pages */

$pagesMenu = new stdClass();
$pagesMenu->create = newMenuItem('Create', '/admin/pages/edit/');
$pagesMenu->find = newMenuItem('Find', '/admin/page/?class=CBAdminPageForPagesFind');
$pagesMenu->trash = newMenuItem('Trash', '/admin/page?class=CBAdminPageForPagesTrash');

if (ColbyUser::current()->isOneOfThe('Developers')) {
    $pagesMenu->develop = newMenuItem('Develop', '/admin/page/?class=CBPagesDevelopmentAdminPage');
    $pagesMenu->develop = (object)[
        'className' => 'CBMenuItem',
        'name' => 'develop',
        'text' => 'Develop',
        'URL' => '/admin/page/?class=CBPagesDevelopmentAdminPage',
    ];
}

$CBAdminMenu->pages = newMenuItem('Pages', '/admin/page/?class=CBAdminPageForPagesFind', $pagesMenu);

/* Models */

$modelsMenu = (object)[
    'directory' => newMenuItem('Directory', '/admin/models/directory/'),
    'import' => newMenuItem('Import', '/admin/page/?class=CBAdminPageForModelImport'),
];

$helpMenu = (object)[
    'markaround-syntax' => (object)[
        'className' => 'CBMenuItem',
        'name' => 'markaround-syntax',
        'text' => 'Markaround',
        'URL' => '/admin/help/markaround-syntax/'
    ],
    'title-subtitle' => (object)[
        'className' => 'CBMenuItem',
        'name' => 'title-subtitle',
        'text' => 'Titles & Descriptions',
        'URL' => '/admin/help/title-subtitle/',
    ],
    'caption-alternative-text' => (object)[
        'className' => 'CBMenuItem',
        'name' => 'caption-alternative-text',
        'text' => 'Captions & Alternative Text',
        'URL' => '/admin/help/caption-alternative-text',
    ],
    'CBArtworkElement' => (object)[
        'className' => 'CBMenuItem',
        'name' => 'CBArtworkElement',
        'text' => 'CBArtworkElement',
        'URL' => '/admin/page/?class=CBAdminPageForCBArtworkElement',
    ],
    'api' => (object)[
        'className' => 'CBMenuItem',
        'name' => 'api',
        'text' => 'API',
        'URL' => '/admin/help/api/',
    ],
    'cssvariables' => (object)[
        'className' => 'CBMenuItem',
        'name' => 'cssvariables',
        'text' => 'CSS Variables',
        'URL' => '/admin/page/?class=CBAdminPageForCSSVariables'
    ],
];

$CBAdminMenu->help = newMenuItem('Help', '/admin/help/markaround-syntax/', $helpMenu);

if (ColbyUser::current()->isOneOfThe('Developers')) {
    $developMenu = new stdClass();
    $developMenu->datastores = newMenuItem('Data Stores', '/admin/page/?class=CBDataStoresAdminPage');
    $developMenu->images = newMenuItem('Images', '/admin/page/?class=CBImagesAdminPage');
    $developMenu->php = newMenuItem('PHP', '/admin/develop/php/');
    $developMenu->update = newMenuItem('Update', '/admin/page/?class=CBAdminPageForUpdate');
    $developMenu->mysql = newMenuItem('MySQL', '/developer/mysql/');

    $CBAdminMenu->develop = newMenuItem('Develop', '/admin/develop/php/', $developMenu);

    $testMenu = (object)[
        'test' => newMenuItem('Website Tests', '/admin/page/?class=CBAdminPageForTests'),
    ];

    $CBAdminMenu->test = newMenuItem('Test', '/admin/page/?class=CBAdminPageForTests', $testMenu);

    $modelsMenu->inspector = newMenuItem('Inspector', '/admin/page/?class=CBAdminPageForModelInspector');
}

$CBAdminMenu->models = newMenuItem('Models', '/admin/models/directory/', $modelsMenu);

/**
 * @return object
 */
function newMenuItem($text, $URL, $submenu = null) {
    return (object)[
        'text' => $text,
        'URL' => $URL,
        'submenu' => $submenu,
    ];
}
