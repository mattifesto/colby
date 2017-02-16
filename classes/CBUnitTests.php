<?php

class CBUnitTests {

    /**
     * @return null;
     */
    public static function getListOfTestsForAjax() {
        $response           = new CBAjaxResponse();
        $response->tests    = [
            ['CBConvert',   'textToStub'],
            ['CBDataStore', 'directoryNameFromDocumentRoot'],
            ['CBDataStore', 'toURL'],
            ['CBDB',        'hex160ToSQL'],
            ['CBDB',        'optional'],
            ['CBDB',        'SQLToArray'],
            ['CBDB',        'SQLToAssociativeArray'],
            ['CBDB',        'SQLToValue'],
            ['CBImages',    'resize'],
            ['CBModelCache'],
            ['CBModels',    'fetchModelByID'],
            ['CBModels',    'fetchModelsByID'],
            ['CBPages',     'stringToDencodedURIPath'],
            ['CBProjection'],
            ['CBSitePreferences'],
            ['CBTestPage'],
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
        return (object)['group' => 'Developers'];
    }

    /**
     * @return void
     */
    public static function test() {

        // Deprecated style

        $testDirectory = CBSystemDirectory . '/snippets/tests';

        include "{$testDirectory}/Test,Colby,decrypt,encrypt.php";

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
