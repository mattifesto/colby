<?php

$currentUserCBID = ColbyUser::getCurrentUserCBID();

$sections = [];

if ($currentUserCBID !== null) {
    array_push(
        $sections,
        (object)[
            'className' => 'CBMessageView',
            'markup' => <<<EOT

                You are currently signed in. It you reset the password for an
                account you will be signed out and signed in as that account.

            EOT,
        ]
    );
}

array_push(
    $sections,
    (object)[
        'className' => 'CBUser_ResetPasswordView',
    ]
);

$pageSpec = CBModelTemplateCatalog::fetchLivePageTemplate(
    (object)[
        'title' => 'Reset Password',
        'sections' => $sections,
    ]
);

CBPage::render(
    CBModel::build(
        $pageSpec
    )
);
