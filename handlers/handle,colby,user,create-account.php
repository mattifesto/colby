<?php

$currentUserCBID = ColbyUser::getCurrentUserCBID();

$sections = [];

if ($currentUserCBID !== null) {
    array_push(
        $sections,
        (object)[
            'className' => 'CBMessageView',
            'markup' => <<<EOT

                You are currently signed in. If you create a new account you
                will be sign out of the current account and signed in as the new
                account.

            EOT,
        ]
    );
}

array_push(
    $sections,
    (object)[
        'className' => 'CBUser_CreateAccountView',
    ]
);

$pageSpec = CBModelTemplateCatalog::fetchLivePageTemplate(
    (object)[
        'title' => 'Create Account',
        'sections' => $sections,
    ]
);

CBPage::render(
    CBModel::build(
        $pageSpec
    )
);
