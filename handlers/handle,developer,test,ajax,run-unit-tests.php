<?php

include CBSystemDirectory . '/classes/CBAjaxResponse.php';


// TODO: Remove these constants.
define('TEST_DOCUMENT_GROUP_ID', '427998e34c31e5410b730cd9993d5cc06bff6132');
define('TEST_DOCUMENT_TYPE_ID',  'd74e2f3d347395acdb627e7c57516c3c4c94e988');
define('TEST_URI', 'test/the-test-document');

$response = new CBAjaxResponse();

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    $response->message = 'You are not authorized to use this feature.';
    $response->send();

    exit;
}

/**
 * Include test snippets.
 */

$testDirectory = COLBY_SYSTEM_DIRECTORY . '/snippets/tests';

include "{$testDirectory}/Test,Colby,decrypt,encrypt.php";
include "{$testDirectory}/Test,ColbyArchive.php";
include "{$testDirectory}/Test,ColbyDocument.php";
include "{$testDirectory}/TestColbyConvert.php";
include "{$testDirectory}/TestColbyMarkaroundParser.php";

/**
 * Send response
 */

$response->wasSuccessful = true;
$response->message = 'The unit tests ran successfully.';

$response->send();


/* ---------------------------------------------------------------- */

/**
 * This function is intended to be used with scalar values.
 */
function CBCompareAnActualTestResultToAnExpectedTestResult($actualTestResult, $expectedTestResult)
{
    if ($actualTestResult !== $expectedTestResult)
    {
        $actualForDisplay = ColbyConvert::textToTextWithVisibleWhitespace($actualTestResult);
        $expectedForDisplay = ColbyConvert::textToTextWithVisibleWhitespace($expectedTestResult);

        throw new RuntimeException("actual: \"{$actualForDisplay}\", expected: \"{$expectedForDisplay}\"");
    }
}
