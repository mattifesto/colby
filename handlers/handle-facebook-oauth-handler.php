<?php

$state = json_decode(urldecode($_GET['state']));

$accessTokenURL = 'https://graph.facebook.com/oauth/access_token' .
    '?client_id=' . COLBY_FACEBOOK_APP_ID .
    '&redirect_uri=' .
        urlencode(COLBY_SITE_URL
            . '/facebook-oauth-handler/') .
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

    throw new RuntimeException('Facebook Authentication Error: ' .
        $object->error->type .
        ', ' .
        $object->error->message);
}

// response is in the form of a query string

$params = null;
parse_str($response, $params);

$userAccessToken = $params['access_token'];
$userAccessExpirationTime = time() + $params['expires'] - 60;

$userPropertiesURL = 'https://graph.facebook.com/me' .
    '?access_token=' . $userAccessToken;

$userProperties = json_decode(file_get_contents($userPropertiesURL));

Colby::useUser();

ColbyUser::loginCurrentUser(
    $userAccessToken,
    $userAccessExpirationTime,
    $userProperties);

header('Location: ' . $state->colby_redirect_uri);
