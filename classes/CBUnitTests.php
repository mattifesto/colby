<?php

class CBUnitTests {

    /**
     * @return void
     */
    public static function runAll() {

        // Deprecated style

        $testDirectory = CBSystemDirectory . '/snippets/tests';

        include "{$testDirectory}/Test,Colby,decrypt,encrypt.php";
        include "{$testDirectory}/Test,ColbyArchive.php";
        include "{$testDirectory}/Test,ColbyDocument.php";

        // New style

        CBUnitTestsForCBDataStore::runAll();
        CBUnitTestsForCBMarkaround::runAll();
        CBUnitTestsForColbyConvert::runAll();
        CBUnitTestsForColbyMarkaroundParser::runAll();
    }

    /**
     * @return void
     */
    public static function runAllForAjax() {
        $response           = new CBAjaxResponse();

        self::runAll();

        $response->wasSuccessful = true;
        $response->message = 'The unit tests ran successfully.';
        $response->send();
    }

    /**
     * @return stdClass
     */
    public static function runAllForAjaxPermissions() {
        $permissions        = new stdClass();
        $permissions->group = 'Developers';

        return $permissions;
    }
}

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
