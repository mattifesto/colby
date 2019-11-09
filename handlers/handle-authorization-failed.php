<?php

$spec = CBModelTemplateCatalog::fetchLivePageTemplate();
$currentUserIsLoggedIn = ColbyUser::currentUserIsLoggedIn();
$spec->title = $currentUserIsLoggedIn ? 'Authorization Failed' : 'Please Log In';
$spec->description = 'You are not authorized to view this page.';

if ($currentUserIsLoggedIn) {
    $message = <<<EOT

        --- p center
        You are not authorized to view this page.
        ---

EOT;

    $spec->sections = [
        (object)[
            'className' => 'CBMessageView',
            'markup' => $message,
        ],
    ];
} else {
    $spec->sections = [
        (object)[
            'className' => 'CBFacebookSignInView',
        ],
    ];
}

CBPage::renderSpec($spec);
