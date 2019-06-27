<?php

/**
 * @NOTE 2014_07_29
 *
 * This file should be used to generate an Ajax response indicating that the
 * user does not have authorization to perform a certain Ajax action either
 * because they are not logged in or they just simply haven't been granted the
 * permission.
 *
 * Any attempts to fix the situation, such as by refreshing the user's login,
 * must take place before this file is included. Once this file is included
 * a response indicating a lack of authorization is certain.
 *
 * Here is some example code making proper use of this file:
 *
 * <?php
 *
 * if (!ColbyUser::current()->isOneOfThe('Administrators')) {
 *     return include cbsysdir() .
 *     '/handlers/handle-authorization-failed-ajax.php'
 * }
 *
 * $response = new CBAjaxResponse();
 *
 * <logic to take action and generate a response for the authorized user>
 */

$response = new CBAjaxResponse();

if (ColbyUser::current()->isLoggedIn()) {
    $response->message = 'You are not authorized to use this feature.';
} else {
    $response->message =
    'You are not authorized to use this feature. This may be because you ' .
    'are not currently not logged in.';
}

$response->send();
