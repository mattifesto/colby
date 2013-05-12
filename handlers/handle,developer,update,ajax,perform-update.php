<?php

$response = new ColbyOutputManager('ajax-response');

$response->begin();

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    $response->message = 'You are not authorized to use this feature.';

    goto done;
}

$updateSnippetFilename = COLBY_SITE_DIRECTORY . '/snippets/update.php';

if (is_file($updateSnippetFilename))
{
    include $updateSnippetFilename;
}

/**
 * Send the response
 */

$response->wasSuccessful = true;
$response->message = "The site was successfully updated.";

done:

$response->end();
