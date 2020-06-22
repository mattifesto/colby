<?php

$userEmailAddress = cb_query_string_value('userEmailAddress');

$currentUserCBID = ColbyUser::getCurrentUserCBID();

$sections = [];

$cbmessage = <<<EOT

    After you submit the form below you will be asked to enter a one time
    password that will be sent to the specified email address. If you do not
    enter the one time password, no modifications will be made to the password.

EOT;

if ($currentUserCBID !== null) {
    $cbmessage .= <<<EOT

        Note: You are currently signed in. If you change/reset the
        password for an email address for a different account you will
        be signed out and signed in as that account.

    EOT;
}

array_push(
    $sections,
    (object)[
        'className' => 'CBMessageView',
        'markup' => $cbmessage,
    ]
);

array_push(
    $sections,
    (object)[
        'className' => 'CBUser_ResetPasswordView',
        'userEmailAddress' => $userEmailAddress,
    ]
);

$pageSpec = CBModelTemplateCatalog::fetchLivePageTemplate(
    (object)[
        'title' => 'Change/Reset Password',
        'sections' => $sections,
    ]
);

CBPage::render(
    CBModel::build(
        $pageSpec
    )
);
