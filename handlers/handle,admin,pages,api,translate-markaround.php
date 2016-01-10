<?php

if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed-ajax.php';
}

$response = new CBAjaxResponse();

/**
 *
 */

$response->contentMarkaround    = $_POST['contentMarkaround'];
$response->contentHTML          = CBMarkaround::markaroundToHTML($response->contentMarkaround);


/**
 * Send the response
 */

$response->wasSuccessful = true;

$response->send();
