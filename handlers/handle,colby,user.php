<?php

$cbmessage = '';
$currentUserCBID = ColbyUser::getCurrentUserCBID();
$currentUserModel = null;

if ($currentUserCBID !== null) {
    $currentUserModel = CBModels::fetchModelByIDNullable(
        $currentUserCBID
    );
}

$viewSpecs = [];

if ($currentUserModel === null) {
    array_push(
        $viewSpecs,
        (object)[
            'className' => 'CBFacebookSignInView',
        ],
        (object)[
            'className' => 'CBSignInView',
        ]
    );
} else {
    $userFullNameAsMessage = CBMessageMarkup::stringToMessage(
        CBModel::valueToString(
            $currentUserModel,
            'title'
        )
    );

    $userEmail = CBModel::valueToString(
        $currentUserModel,
        'email'
    );

    if ($userEmail === '') {
        $userEmail = 'no email address';
    }

    $userEmailAsMessage = CBMessageMarkup::stringToMessage(
        $userEmail
    );

    array_push(
        $viewSpecs,
        (object)[
            'className' => 'CBMessageView',
            'markup' => <<<EOT

                You are currently logged in as {$userFullNameAsMessage}
                \({$userEmailAsMessage}\).

            EOT,
        ],
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
