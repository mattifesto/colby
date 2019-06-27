<?php

ColbyUser::logoutCurrentUser();

if (isset($_GET['state'])) {
    $state = json_decode(urldecode($_GET['state']));

    header('Location: ' . $state->colby_redirect_uri);
} else {
    header('Location: ' . cbsiteurl() . '/');
}
