<?php

(function () {

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

    $userFullName = CBUser::getName(
        $userModel
    );

    $cbmessage = <<<EOT

        --- h1
        {$userFullName}
        ---

        (edit profile (a /colby/user/))

    EOT;


    CBPage::renderSpec(
        CBModelTemplateCatalog::fetchLivePageTemplate(
            (object)[
                'title' => $userFullName,
                'sections' => [
                    (object)[
                        'className' => 'CBMessageView',
                        'markup' => $cbmessage,
                    ]
                ]
            ]
        )
    );

})();
