<?php

$currentUserCBID = ColbyUser::getCurrentUserCBID();

/**
 * If the user isn't signed in redirect them to the sign in page.
 */

if ($currentUserCBID === null) {
    header(
        'Location: ' .
        CBUser::getSignInPageURL()
    );

    exit();
}

$pageSpec = CBViewPage::standardPageTemplate();

CBModel::merge(
    $pageSpec,
    (object)[
        'title' => 'User',
        'sections' => [
            (object)[
                'className' => 'CBCurrentUserView',
            ],
        ],
    ]
);

CBPage::render(
    CBModel::build(
        $pageSpec
    )
);
