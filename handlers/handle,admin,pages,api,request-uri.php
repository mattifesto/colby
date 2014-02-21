<?php

include_once CBSystemDirectory . '/classes/CBPages.php';


$response = new ColbyOutputManager('ajax-response');

$response->begin();


/**
 *
 */

if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    $response->message = 'You are not authorized to use this feature.';

    goto done;
}


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

done:

$response->end();
