<?php

/**
 * After the browser is sent to the Facebook login page, the browser will be
 * redirected to this page by Facebook. Regardless of the succes of Facebook
 * authentication the browser will be redirected to the redirect URI specified
 * when the process began. If they are not logged in, they will not see the
 * page.
 */

// If the user cancels or any error occurs Facebook authentication has been
// denied.
if (isset($_GET['error'])) {
    goto done;
}

$accessTokenObject = CBFacebook::fetchAccessTokenObject($_GET['code']);
$userProperties = CBFacebook::fetchUserProperties($accessTokenObject->access_token);

/**
 * NOTE: 2013.10.24
 * This is to address an issue one user is having that when they log
 * in everything seems to work but the user properties don't contain a `name`
 * property. There is no repro for this issue.
 */

if (!isset($userProperties->name)) {
    throw new RuntimeException('The user properties do not contain a name: ' .
                               serialize($userProperties) .
                               ' User properties retrieval URL: ' .
                               $userPropertiesURL .
                               ' HTTP code: ' .
                               $httpCode);
}

ColbyUser::loginCurrentUser(
    $accessTokenObject->access_token,
    time() + $accessTokenObject->expires_in - 60,
    $userProperties);

done:

/**
 * Decode the state that we sent into the original login request. It contains
 * the URI from which the user initiated the login request.
 */

$state = json_decode(urldecode($_GET['state']));

header('Location: ' . $state->colby_redirect_uri);
