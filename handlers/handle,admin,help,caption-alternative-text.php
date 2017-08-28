<?php

if (!ColbyUser::current()->isOneOfThe('Administrators')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

$parsedown = new Parsedown();
$contentAsCommonMark = file_get_contents(__DIR__ . '/handle,admin,help,caption-alternative-text.md');
$contentAsHTML = $parsedown->text($contentAsCommonMark);
$model = (object)[
    'className' => 'CBViewPage',
    'classNameForSettings' => 'CBPageSettingsForAdminPages',
    'titleHTML' => 'Captions and Alternative Text Help',
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
            'contentAsHTML' => $contentAsHTML,
        ],
    ],
];

CBPage::render($model);
