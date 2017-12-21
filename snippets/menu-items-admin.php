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
    'directory' => newMenuItem('Directory', '/admin/?c=CBModelsAdmin'),
    'import' => newMenuItem('Import', '/admin/page/?class=CBAdminPageForModelImport'),
];

$CBAdminMenu->help = newMenuItem('Help', '/admin/?c=CBDocumentation&p=TitlesAndDescriptions');

if (ColbyUser::current()->isOneOfThe('Developers')) {
    $developMenu = (object)[
        'datastores' => (object)[
            'className' => 'CBMenuItem',
            'name' => 'datastores',
            'text' => 'Data Stores',
            'URL' => '/admin/page/?class=CBDataStoresAdminPage',
        ],
        'images' => (object)[
            'className' => 'CBMenuItem',
            'name' => 'images',
            'text' => 'Images',
            'URL' => '/admin/page/?class=CBImagesAdminPage',
        ],
        'php' => (object)[
            'className' => 'CBMenuItem',
            'name' => 'php',
            'text' => 'PHP',
            'URL' => '/admin/develop/php/',
        ],
        'test' => (object)[
            'className' => 'CBMenuItem',
            'name' => 'test',
            'text' => 'Test',
            'URL' => '/admin/page/?class=CBAdminPageForTests',
        ],
        'update' => (object)[
            'className' => 'CBMenuItem',
            'name' => 'update',
            'text' => 'Update',
            'URL' => '/admin/page/?class=CBAdminPageForUpdate',
        ],
    ];

    $CBAdminMenu->develop = newMenuItem('Develop', '/admin/page/?class=CBAdminPageForUpdate', $developMenu);

    $modelsMenu->inspector = newMenuItem('Inspector', '/admin/?c=CBModelInspector');
}

$CBAdminMenu->models = newMenuItem('Models', '/admin/?c=CBModelsAdmin', $modelsMenu);

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
