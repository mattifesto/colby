<?php

if (!ColbyUser::current()->isOneOfThe('Administrators')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

$spec = (object)[
    'className' => 'CBViewPage',
    'classNameForSettings' => 'CBPageSettingsForAdminPages',
    'title' => 'Titles and Descriptions Help',
    'layout' => (object)[
        'className' => 'CBPageLayout',
        'customLayoutClassName' => 'CBAdminPageLayout',
        'customLayoutProperties' => (object)[
            'selectedMenuItemName' => 'help',
            'selectedSubmenuItemName' => 'title-description',
        ],
    ],
    'sections' => [
        (object)[
            'className' => 'CBMessageView',
            'markup' => file_get_contents(__DIR__ . '/handle,admin,help,title-description.mmk'),
        ],
    ],
];

CBPage::renderSpec($spec);
