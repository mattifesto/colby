<?php

$state = $_GET['state'];

setcookie(
    CBFacebook::loginStateCookieName,
    $state,
    time() + (60 * 60 * 24 * 30),
    '/'
);

header(
    'Location: ' .
    CBFacebook::loginURLForFacebook()
);
