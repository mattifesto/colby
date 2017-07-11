<?php

if (!ColbyUser::current()->isOneOfThe('Administrators')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

$contentAsMarkaround = file_get_contents(__DIR__ . '/handle,admin,help,markaround-syntax.markaround');
$contentAsHTML = CBMarkaround::markaroundToHTML($contentAsMarkaround);
$model = (object)[
    'classNameForSettings' => 'CBPageSettingsForAdminPages',
    'titleHTML' => 'Markaround Help',
    'layout' => (object)[
        'className' => 'CBPageLayout',
        'customLayoutClassName' => 'CBAdminPageLayout',
        'customLayoutProperties' => (object)[
            'selectedMenuItemName' => 'help',
            'selectedSubmenuItemName' => 'markaround-syntax',
        ],
    ],
    'sections' => [
        (object)[
            'className' => 'CBTextView2',
            'contentAsHTML' => $contentAsHTML,
        ],
    ],
];

CBViewPage::renderModelAsHTML($model);
