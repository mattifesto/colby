<?php

// TODO: Remove these constants.
define('TEST_DOCUMENT_GROUP_ID', '427998e34c31e5410b730cd9993d5cc06bff6132');
define('TEST_DOCUMENT_TYPE_ID',  'd74e2f3d347395acdb627e7c57516c3c4c94e988');
define('TEST_URI', 'test/the-test-document');

$response = new ColbyOutputManager('ajax-response');

$response->begin();

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    $response->message = 'You are not authorized to use this feature.';

    goto done;
}

/**
 * Include test snippets.
 */

$testDirectory = COLBY_SYSTEM_DIRECTORY . '/snippets/tests';

include "{$testDirectory}/Test,Colby,siteSchemaVersionNumber.php";
include "{$testDirectory}/Test,ColbyArchive.php";
include "{$testDirectory}/Test,ColbyDocument.php";
include "{$testDirectory}/TestColbyMarkaroundParser.php";

/**
 * Send response
 */

$response->wasSuccessful = true;
$response->message = 'The unit tests ran successfully.';

done:

$response->end();
