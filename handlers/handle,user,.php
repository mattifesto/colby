<?php

return (function () {

    $stubs = ColbyRequest::decodedStubs();

    if (
        count($stubs) !== 2
    ) {
        return 0;
    }

    $requestedPrettyUsername = $stubs[1];

    if (
        !CB_Username::isPrettyUsernameValid(
            $requestedPrettyUsername
        )
    ) {
        return 0;
    }

    $userModelCBID = CBUser::prettyUsernameToUserModelCBID(
        $requestedPrettyUsername
    );

    if (
        $userModelCBID === null
    ) {
        return 0;
    }

    $userModel = CBModelCache::fetchModelByID(
        $userModelCBID
    );

    $userModelPrettyUsername = CBUser::getPrettyUsername(
        $userModel
    );

    if (
        $requestedPrettyUsername !== $userModelPrettyUsername
    ) {
        header(
            "Location: /user/{$userModelPrettyUsername}/",
            true,
            301
        );

        return 1;
    }

    $currentUserModelCBID = ColbyUser::getCurrentUserCBID();

    if (
        $currentUserModelCBID !== $userModelCBID
    ) {
        $userPublicProfileIsEnabled = CBUser::getPublicProfileIsEnabled(
            $userModel
        );

        if (
            $userPublicProfileIsEnabled !== true
        ) {
            return 0;
        }
    }

    $userFullName = CBUser::getName(
        $userModel
    );

    $cbmessage = <<<EOT

        --- h1
        {$userFullName}
        ---

    EOT;

    if (
        $userModelCBID === $currentUserModelCBID
    ) {
        $cbmessage .= <<<EOT

            (edit profile (a /colby/user/))

        EOT;
    }

    $pageSpec = CBViewPage::standardPageTemplate();

    $views =
    [];

    $messageViewSpec =
    CBModel::createSpec(
        'CBMessageView'
    );

    CBMessageView::setCBMessage(
        $messageViewSpec,
        $cbmessage
    );

    array_push(
        $views,
        $messageViewSpec
    );

    $viewSpec = CBModel::createSpec(
        'CB_CBView_UserMomentList'
    );

    CB_CBView_UserMomentList::setUserModelCBID(
        $viewSpec,
        $userModelCBID
    );

    CB_CBView_UserMomentList::setShowMomentCreator(
        $viewSpec,
        true
    );

    array_push(
        $views,
        $viewSpec
    );

    CBModel::merge(
        $pageSpec,
        (object)[
            'title' => $userFullName,
            'sections' => $views,
        ]
    );

    CBPage::renderSpec(
        $pageSpec
    );

    return 1;

})();
