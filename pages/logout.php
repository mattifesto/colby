<?php

$state = json_decode(urldecode($_GET['state']));
$time = time() - (60 * 60 * 24);

setcookie('user_id', '', $time, '/');
setcookie('user_access_token', '', $time, '/');
setcookie('user_name', '', $time, '/');

header('Location: ' . $state->colby_redirect_uri);
