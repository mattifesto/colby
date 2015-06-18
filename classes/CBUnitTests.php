<?php

class CBUnitTests {

    /**
     * @return null;
     */
    public static function getListOfTestsForAjax() {
        $response           = new CBAjaxResponse();
        $response->tests    = [
            ['CBDataStore', 'directoryNameFromDocumentRoot'],
            ['CBDataStore', 'toURL'],
            ['CBDB',        'hex160ToSQL'],
            ['CBDB',        'optional'],
            ['CBDB',        'SQLToArray'],
            ['CBDB',        'SQLToAssociativeArray'],
            ['CBDB',        'SQLToValue'],
            ['CBModels',    'fetchModelForID'],
            ['CBPages',     'updateURIs'],
            ['CBUnit'],
            ['CBViewPage',  'save']];

        if (is_callable($function = 'CBTests::tests')) {
            $response->tests = array_merge($response->tests, call_user_func($function));
        }

        $response->wasSuccessful = true;
        $response->send();
    }

    /**
     * @return {stdClass}
     */
    public static function getListOfTestsForAjaxPermissions() {
        return (object)['group' => 'Testers'];
    }

    /**
     * @return void
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
