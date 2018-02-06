<?php

if (!ColbyUser::current()->isOneOfThe('Administrators')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

$contentAsCommonMark = file_get_contents(__DIR__ . '/handle,admin,help,caption-alternative-text.md');
$spec = (object)[
    'className' => 'CBViewPage',
    'classNameForSettings' => 'CBPageSettingsForAdminPages',
    'title' => 'Captions and Alternative Text Help',
    'layout' => (object)[
        'className' => 'CBPageLayout',
        'customLayoutClassName' => 'CBAdminPageLayout',
        'customLayoutProperties' => (object)[
            'selectedMenuItemName' => 'help',
            'selectedSubmenuItemName' => 'caption-alternative-text',
        ],
    ],
    'sections' => [
        (object)[
            'className' => 'CBTextView2',
            'contentAsCommonMark' => $contentAsCommonMark,
        ],
    ],
];

CBPage::renderSpec($spec);
