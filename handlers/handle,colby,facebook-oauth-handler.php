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
 *
 * NOTE: 2017.05.25
 * In the future give the user a more helpful message and log the current
 * exception message for the admin to see.
 */

if (!isset($userProperties->name)) {
    throw new RuntimeException('The Facebook user properties do not contain a "name" property: ' . json_encode($userProperties));
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

try {
    $state = json_decode($_COOKIE[CBFacebook::loginStateCookieName]);
    $location = $state->colby_redirect_uri;
} catch (Throwable $throwable) {
    /**
     * 2017.12.03 Previously this exception was marked as severity 5. The new
     * report() function does not allow the caller to specify severity. The
     * attempt to lower the severity of the exception may mean that this
     * situation is really not that important and should not throw an exception.
     * Document further changes to this code.
     */

    CBErrorHandler::report($throwable);
    $location = '/';
}

header('Location: ' . $location);
