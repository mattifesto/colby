<?php

$currentUserIsLoggedIn = ColbyUser::currentUserIsLoggedIn();

$pageTitle = (
    $currentUserIsLoggedIn ?
    'Authorization Failed' :
    'Please Log In'
);

$viewSpecs = [];

if ($currentUserIsLoggedIn) {
    $viewSpecs[] = (object)[
        'className' => 'CBMessageView',
        'markup' => <<<EOT
            --- p center
            You are not authorized to view this page.
            ---
        EOT,
    ];
} else {
    array_push(
        $viewSpecs,
        (object)[
            'className' => 'CBFacebookSignInView',
        ],
        (object)[
            'className' => 'CBSignInView',
        ]
    );
}

$pageSpec = CBModelTemplateCatalog::fetchLivePageTemplate(
    (object)[
        'title' => $pageTitle,
        'description' => 'You are not authorized to view this page.',
        'sections' => $viewSpecs,
    ]
);

CBPage::renderSpec(
    CBModel::build(
        $pageSpec
    )
);
