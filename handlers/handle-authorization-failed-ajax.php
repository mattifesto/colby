<?php

$response = new ColbyOutputManager('ajax-response');

$response->begin();

if (ColbyUser::current()->isLoggedIn())
{
    $response->message = 'You are not authorized to use this feature.';
}
else
{
    $response->message = 'You are not authorized to use this feature. This may be because you are currently not logged in.';
}

$response->end();
