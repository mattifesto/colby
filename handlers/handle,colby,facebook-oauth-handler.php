<?php

/**
 * After the browser is sent to the Facebook login page, the browser will be
 * redirected to this page by Facebook. Regardless of the success of Facebook
 * authentication the browser will be redirected to the redirect URI specified
 * when the process began. If they are not logged in, they will not see the
 * page.
 */



/**
 * Decode the state that we sent into the original login request. It contains
 * the URI from which the user initiated the login request.
 */

try {
    $stateAsJSON = cb_query_string_value('state');
    $state = json_decode($stateAsJSON);

    $destinationURL = CBModel::valueToString(
        $state,
        'destinationURL'
    );
} catch (Throwable $throwable) {
    CBErrorHandler::report($throwable);

    $destinationURL = '/';
}



/**
 * If the user cancels or any error occurs Facebook authentication has been
 * denied.
 */

if (isset($_GET['error'])) {
    goto done;
}

$accessTokenObject = CBFacebook::fetchAccessTokenObject(
    $_GET['code'],
    $destinationURL
);

$facebookAccessToken = $accessTokenObject->access_token;

$facebookUserProperties = CBFacebook::fetchUserProperties(
    $facebookAccessToken
);

$facebookUserID = CBModel::valueAsInt(
    $facebookUserProperties,
    'id'
);

$facebookName = trim(
    CBModel::valueToString(
        $facebookUserProperties,
        'name'
    )
);

/**
 * @NOTE 2013_10_24
 *
 *      This is to address an issue one user is having that when they log in
 *      everything seems to work but the user properties don't contain a `name`
 *      property. There is no repro for this issue.
 *
 * @NOTE 2017_05_25
 *
 *      In the future give the user a more helpful message and log the current
 *      exception message for the admin to see.
 */
if ($facebookName === '') {
    throw CBException::createWithValue(
        (
            'The user properties provided by Facebook do not contain ' .
            'a "name" property.'
        ),
        $facebookUserProperties,
        'e1a1bd35ad3811df94ac1b3dbaa8c088552bc089'
    );
}

ColbyUser::loginFacebookUser(
    $facebookUserID,
    $facebookAccessToken,
    $facebookName
);



done:



header(
    'Location: ' .
    $destinationURL
);
