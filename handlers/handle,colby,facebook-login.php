<?php

/**
 * @NOTE 2020_01_28
 *
 *      Visiting /colby/facebook-login/ when you are already signed in will
 *      behave as if you are not already signed in. This is probably not the
 *      correct behavior, but we are currently hardening the sign in/sign out
 *      processes since signing in using email has recently been added.
 */



/**
 * @NOTE 2020_01_28
 *
 *      The "state" query variable should hold JSON for an object with a
 *      "colby_redirect_uri" property indicating the URL to go to if login
 *      succeeds.
 */
$stateAsJSON = cb_query_string_value('state');

setcookie(
    CBFacebook::loginStateCookieName,
    $stateAsJSON,
    time() + (60 * 60 * 24 * 30),
    '/'
);

header(
    'Location: ' .
    CBFacebook::loginURLForFacebook()
);
