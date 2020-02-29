<?php

/**
 * This handler is included when a user doesn't have authorization to view a
 * another page. It will either redirect to the sign in page or display a
 * message stating that the user is not authorized to view the page.
 */

$currentUserCBID = ColbyUser::getCurrentUserCBID();

/**
 * If the user isn't signed in redirect them to the sign in page.
 */

if ($currentUserCBID === null) {
    $signInPageURL = CBUser::getSignInPageURL();

    header(
        "Location: {$signInPageURL}",
        true,
        302
    );

    exit();
}



$pageSpec = CBModelTemplateCatalog::fetchLivePageTemplate(
    (object)[
        'title' => 'Authorization Failed',
        'description' => 'You are not authorized to view this page.',
        'sections' => [
            (object)[
                'className' => 'CBMessageView',
                'markup' => <<<EOT
                    --- p center
                    You are not authorized to view this page.
                    ---
                EOT,
            ],
        ],
    ]
);

CBPage::renderSpec(
    CBModel::build(
        $pageSpec
    )
);
