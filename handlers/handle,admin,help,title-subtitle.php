<?php

if (!ColbyUser::current()->isOneOfThe('Administrators')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

$parsedown = new Parsedown();
$contentAsCommonMark = file_get_contents(__DIR__ . '/handle,admin,help,title-subtitle.md');
$contentAsHTML = $parsedown->text($contentAsCommonMark);
$model = (object)[
    'classNameForSettings' => 'CBPageSettingsForAdminPages',
    'titleHTML' => 'Titles and Descriptions Help',
    'layout' => (object)[
        'className' => 'CBPageLayout',
        'customLayoutClassName' => 'CBAdminPageLayout',
        'customLayoutProperties' => (object)[
            'selectedMenuItemName' => 'help',
            'selectedSubmenuItemName' => 'title-subtitle',
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
