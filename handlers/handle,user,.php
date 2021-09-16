<?php

$stubs = ColbyRequest::decodedStubs();

if (
    count($stubs) !== 2
) {
    return 0;
}

$userCBID = $stubs[1];

if (
    !CBID::valueIsCBID($userCBID)
) {
    return 0;
}

$userModel = CBModelCache::fetchModelByID(
    $userCBID
);

if (
    $userModel === null ||
    CBModel::getClassName($userModel) !== 'CBUser'
) {
    return 0;
}

$userName = CBUser::getName(
    $userModel
);

$cbmessage = <<<EOT

    --- h1
    {$userName}
    ---

    (edit profile (a /colby/user/))

EOT;


CBPage::renderSpec(
    CBModelTemplateCatalog::fetchLivePageTemplate(
        (object)[
            'title' => $userName,
            'sections' => [
                (object)[
                    'className' => 'CBMessageView',
                    'markup' => $cbmessage,
                ]
            ]
        ]
    )
);
