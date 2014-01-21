<?php

$response = new ColbyOutputManager('ajax-response');

$response->begin();

if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    $response->message = 'You are not authorized to use this feature.';

    goto done;
}

/**
 * TODO: Query the database to find the page and get the model.
 */

/**
 * Send the response
 */

$response->wasSuccessful = true;

done:

$response->end();
