<?php

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

$response->contentMarkaround    = $_POST['contentMarkaround'];
$response->contentHTML          = ColbyConvert::markaroundToHTML($response->contentMarkaround);


/**
 * Send the response
 */

$response->wasSuccessful = true;

done:

$response->end();
