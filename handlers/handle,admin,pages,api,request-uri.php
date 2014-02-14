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

$pageRowID          = $_POST['rowID'];
$pageRowIDForSQL    = (int)$pageRowID;
$requestedURI       = $_POST['URI'];
$requestedURIForSQL = ColbyConvert::textToSQL($requestedURI);

$sql = <<<EOT

    UPDATE
        `ColbyPages`
    SET
        `URI` = '{$requestedURIForSQL}'
    WHERE
        `ID` = {$pageRowIDForSQL}

EOT;

try
{
    Colby::query($sql);
}
catch (Exception $exception)
{
    if (1062 == Colby::mysqli()->errno)
    {
        $response->URIWasGranted    = false;
        $response->wasSuccessful    = true;

        goto done;
    }
    else
    {
        throw $exception;
    }
}


/**
 * Send the response
 */

$response->URI              = $requestedURI;
$response->URIWasGranted    = true;
$response->wasSuccessful    = true;

done:

$response->end();
