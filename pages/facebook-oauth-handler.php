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

$userPropertiesURL = 'https://graph.facebook.com/me' .
    '?access_token=' . $params['access_token'];

$userProperties = json_decode(file_get_contents($userPropertiesURL));

setcookie('user_id', $userProperties->id, 0, '/');
setcookie('user_access_token', $params['access_token'], 0, '/');
setcookie('user_name', $userProperties->name, 0, '/');

header('Location: ' . $state->colby_redirect_uri);
