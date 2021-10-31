<?php

return (function () {

    $stubs = ColbyRequest::decodedStubs();

    if (
        count($stubs) !== 2
    ) {
        return 0;
    }

    $prettyUsername = $stubs[1];

    if (
        !CB_Username::isPrettyUsernameValid(
            $prettyUsername
        )
    ) {
        return 0;
    }

    $usernameModelCBID = CB_Username::prettyUsernameToUsernameModelCBID(
        $prettyUsername
    );

    if (
        $usernameModelCBID === null
    ) {
        return 0;
    }

    $userModelCBID = CB_Username::fetchUserCBIDByUsernameCBID(
        $usernameModelCBID
    );

    if ($userModelCBID === null) {
        return 0;
    }

    $userModel = CBModelCache::fetchModelByID(
        $userModelCBID
    );

    $userPublicProfileIsEnabled = CBUser::getPublicProfileIsEnabled(
        $userModel
    );

    if (
        $userPublicProfileIsEnabled !== true
    ) {
        return 0;
    }

    $userFullName = CBUser::getName(
        $userModel
    );

    $cbmessage = <<<EOT

        --- h1
        {$userFullName}
        ---

    EOT;

    $currentUserModelCBID = ColbyUser::getCurrentUserCBID();

    if (
        $userModelCBID === $currentUserModelCBID
    ) {
        $cbmessage .= <<<EOT

            (edit profile (a /colby/user/))

        EOT;
    }

    $pageSpec = CBViewPage::standardPageTemplate();

    CBModel::merge(
        $pageSpec,
        (object)[
            'title' => $userFullName,
            'sections' => [
                (object)[
                    'className' => 'CBMessageView',
                    'markup' => $cbmessage,
                ]
            ]
        ]
    );

    CBPage::renderSpec(
        $pageSpec
    );

    return 1;
})();
