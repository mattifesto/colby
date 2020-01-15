<?php

$currentUserCBID = ColbyUser::getCurrentUserCBID();

$viewSpecs = [
    (object)[
        'className' => 'CBCurrentUserView',
    ],
];

if ($currentUserCBID === null) {
    array_push(
        $viewSpecs,
        (object)[
            'className' => 'CBFacebookSignInView',
        ],
        (object)[
            'className' => 'CBSignInView',
        ],
    );
} else {
    array_push(
        $viewSpecs,
        (object)[
            'className' => 'CBSignOutView',
        ]
    );
}

$pageSpec = CBModelTemplateCatalog::fetchLivePageTemplate(
    (object)[
        'title' => 'User',
        'sections' => $viewSpecs,
    ]
);

CBPage::render(
    CBModel::build(
        $pageSpec
    )
);
