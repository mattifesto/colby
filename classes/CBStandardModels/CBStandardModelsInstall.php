<?php

$specs = [
    (object)[
        'ID' => CBStandardModels::CBMenuIDForMainMenu,
        'className' => 'CBMenu',
        'title' => 'Main Menu',
    ],
    (object)[
        'ID' => CBStandardModels::CBThemeIDForCBMenuViewForMainMenu,
        'className' => 'CBTheme',
        'classNameForKind' => 'CBMenuView',
        'title' => 'Standard Main Menu',
    ],
    (object)[
        'ID' => CBStandardModels::CBThemeIDForCBPageTitleAndDescriptionView,
        'className' => 'CBTheme',
        'classNameForKind' => 'CBPageTitleAndDescriptionView',
        'title' => 'Standard',
    ],
    (object)[
        'ID' => CBStandardModels::CBThemeIDForCBTextViewForBodyText,
        'className' => 'CBTheme',
        'classNameForKind' => 'CBTextView',
        'title' => 'Standard Body Text',
    ],
];

$IDs = array_map(function ($spec) { return $spec->ID; }, $specs);
$models = CBModels::fetchModelsByID($IDs);

foreach ($specs as $spec) {
    if (empty($models[$spec->ID])) {
        CBModels::save([$spec]);
    }
}
