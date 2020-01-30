<?php

$currentUserCBID = ColbyUser::getCurrentUserCBID();

$viewSpecs = [];

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
            'className' => 'CBCurrentUserView',
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
