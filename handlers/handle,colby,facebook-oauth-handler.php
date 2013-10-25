<?php

/**
 * After the user is sent to the Facebook login page, the user will be
 * redirected to this page. If the user has refused to allow us to log them in,
 * we will immediately redirect them back to the page where they clicked the
 * login link which will look the same as when they left it. If they do allow
 * us to log them in they will be logged in before being redirected to the page
 * which will now either show them the content or let them know that they don't
 * have permissions to view the page.
 *
 * Just because a user successfully logs in doesn't mean they have permissions
 * to see that page.
 */

/**
 * Check to see if the user didn't accept the Login dialog and clicked Cancel.
 */

if (isset($_GET['error']))
{
    /**
     * Return the user to the page they started at because they decided not
     * to give us permission to log them in. (They chose "Cancel".)
     */

    goto done;
}

/**
 * Exchange the code we received for an access token for the user.
 */

$accessTokenURL = 'https://graph.facebook.com/oauth/access_token' .
    '?client_id=' . COLBY_FACEBOOK_APP_ID .
    '&redirect_uri=' .
        urlencode(COLBY_SITE_URL
            . '/colby/facebook-oauth-handler/') .
    '&client_secret=' . COLBY_FACEBOOK_APP_SECRET .
    '&code=' . $_GET['code'];

$curlHandle = curl_init();
curl_setopt($curlHandle, CURLOPT_URL, $accessTokenURL);
curl_setopt($curlHandle, CURLOPT_HEADER, 0);
curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);

$response = curl_exec($curlHandle);

if (curl_getinfo($curlHandle, CURLINFO_HTTP_CODE) === 400)
{
    $object = json_decode($response);

    throw new RuntimeException('Error retrieving Facebook access token: ' .
        $object->error->type .
        ', ' .
        $object->error->message);
}

curl_close($curlHandle);

/**
 * The access token request returns a successful response in the form of a
 * query string which has a query variable containing the access token.
 */

$params = null;
parse_str($response, $params);

$userAccessToken = $params['access_token'];
$userAccessExpirationTime = time() + $params['expires'] - 60;

/**
 * Use the access token to get the user's information from Facebook.
 */

$userPropertiesURL = "https://graph.facebook.com/me?access_token={$userAccessToken}";

$curlHandle = curl_init();
curl_setopt($curlHandle, CURLOPT_URL, $userPropertiesURL);
curl_setopt($curlHandle, CURLOPT_HEADER, 0);
curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);

$response = curl_exec($curlHandle);

$httpCode = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);

if ($httpCode === 400)
{
    $object = json_decode($response);

    throw new RuntimeException('Error retrieving Facebook user properties: ' .
        $object->error->type .
        ', ' .
        $object->error->message);
}

curl_close($curlHandle);

/**
 * Decode the user properties and log the user in.
 */

$userProperties = json_decode($response);

/**
 * 2013.10.24.1201
 * ISSUE: This is to address an issue one user is having that when they log
 * in everything seems to work but the user properties don't contain a `name`
 * property. There is no repro for this issue.
 */

if (!isset($userProperties->name))
{
    throw new RuntimeException('The user properties do not contain a name: ' .
                               serialize($userProperties) .
                               ' User properties retrieval URL: ' .
                               $userPropertiesURL .
                               ' HTTP code: ' .
                               $httpCode);
}

ColbyUser::loginCurrentUser(
    $userAccessToken,
    $userAccessExpirationTime,
    $userProperties);

done:

/**
 * Decode the state that we sent into the original login request. It contains
 * the URI from which the user initiated the login request.
 */

$state = json_decode(urldecode($_GET['state']));

header('Location: ' . $state->colby_redirect_uri);
