<?php

class CBUnitTests {

    public static function getListOfTestsForAjax() {
        $response           = new CBAjaxResponse();
        $response->tests    = [
            ['CBDB',        'SQLToArray'],
            ['CBUnit'],
            ['CBViewPage',  'save']];

        if (is_callable($function = 'CBTests::tests')) {
            $response->tests = array_merge($response->tests, call_user_func($function));
        }

        $response->wasSuccessful = true;
        $response->send();
    }

    public static function getListOfTestsForAjaxPermissions() {
        return (object)['group' => 'Testers'];
    }

    /**
    @return void
    */
    public static function test() {

        // Deprecated style

        $testDirectory = CBSystemDirectory . '/snippets/tests';

        include "{$testDirectory}/Test,Colby,decrypt,encrypt.php";
        include "{$testDirectory}/Test,ColbyArchive.php";
        include "{$testDirectory}/Test,ColbyDocument.php";

        // New style

        CBUnitTestsForCBDataStore::runAll();
        CBUnitTestsForCBMarkaround::runAll();
        CBUnitTestsForCBView::runAll();
        CBUnitTestsForColbyConvert::runAll();
        CBUnitTestsForColbyMarkaroundParser::runAll();

        return 'The unit tests ran successfully.';
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
