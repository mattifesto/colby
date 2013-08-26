<?php

$response = new ColbyOutputManager('ajax-response');

$response->begin();

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    $response->message = 'You are not authorized to use this feature.';

    goto done;
}

if ($filename = Colby::findFile('setup/update.php'))
{
    include $filename;
}

/**
 * Send the response
 */

$response->wasSuccessful = true;
$response->message = "The site was successfully updated.";

done:

$response->end();
