<?php

$state = json_decode(urldecode($_GET['state']));

$accessTokenURL = 'https://graph.facebook.com/oauth/access_token' .
    '?client_id=' . COLBY_FACEBOOK_APP_ID .
    '&redirect_uri=' .
        urlencode(COLBY_SITE_URL
            . 'facebook-oauth-handler/') .
    '&client_secret=' . COLBY_FACEBOOK_APP_SECRET .
    '&code=' . $_GET['code'];

$response = file_get_contents($accessTokenURL);

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
    $userProperties->id,
    $userAccessToken,
    $userAccessExpirationTime,
    $userProperties->name,
    $userProperties->first_name);

header('Location: ' . $state->colby_redirect_uri);
