<?php


$pageSpec = CBModelTemplateCatalog::fetchLivePageTemplate();


/* title */

$searchQuery = cb_query_string_value('search-for');
$searchQueryHTML = cbhtml($searchQuery);
$title = 'Search';

if ($searchQueryHTML) {
    $title = "{$title}: {$searchQuery}";
}

CBViewPage::setTitle(
    $pageSpec,
    $title
);


/* views */

$viewSpecs = [
    (object)[
        'className' => 'CBView_CBSearchForm',
    ],
    (object)[
        'className' => 'CBView_CBSearchResults',
    ],
];

CBViewPage::setViews(
    $pageSpec,
    $viewSpecs
);


/* render */

CBPage::renderSpec(
    $pageSpec
);
