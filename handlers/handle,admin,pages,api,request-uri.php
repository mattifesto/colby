<?php

if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed-ajax.php';
}

$response = new CBAjaxResponse();

/**
 *
 */

$rowID          = $_POST['rowID'];
$requestedURI   = $_POST['URI'];


/**
 *
 */

$response->URIWasGranted = CBPages::tryUpdateRowURI($rowID, $requestedURI);


/**
 * Send the response
 */

$response->URI              = $requestedURI;
$response->wasSuccessful    = true;

$response->send();
