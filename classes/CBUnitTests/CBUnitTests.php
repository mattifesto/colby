<?php

class CBUnitTests {

    /**
     * @return null
     */
    static function getListOfTestsForAjax() {
        $response = new CBAjaxResponse();
        $response->tests = [
            ['CBConvert',               'textToStub'],
            ['CBDataStore',             'createAndDelete'],
            ['CBDataStore',             'directoryNameFromDocumentRoot'],
            ['CBDataStore',             'toURL'],
            ['CBDataStore',             'URIToID'],
            ['CBDB',                    'hex160ToSQL'],
            ['CBDB',                    'optional'],
            ['CBDB',                    'SQLToArray'],
            ['CBDB',                    'SQLToAssociativeArray'],
            ['CBDB',                    'SQLToValue'],
            ['CBImages',                'resize'],
            ['CBModel',                 'toModel'],
            ['CBModel',                 'toModelMinimalImplementation'],
            ['CBModelCache'],
            ['CBModels',                'fetchModelByID'],
            ['CBModels',                'fetchModelsByID'],
            ['CBModels',                'saveNullableModel'],
            ['CBModels',                'saveSpecWithoutID'],
            ['CBPages',                 'stringToDencodedURIPath'],
            ['CBProjection'],
            ['CBSitePreferences'],
            ['CBTestPage'],
            ['CBUnit'],
            ['CBViewPage',              'save'],
            ['CBPageVerificationTask',  'importThumbnailURLToImage'],
            ['CBPageVerificationTask',  'upgradeThumbnailURLToImage'],
        ];

        if (is_callable($function = 'CBTests::tests')) {
            $response->tests = array_merge($response->tests, call_user_func($function));
        }

        $response->wasSuccessful = true;
        $response->send();
    }

    /**
     * @return object
     */
    static function getListOfTestsForAjaxPermissions() {
        return (object)['group' => 'Developers'];
    }

    /**
     * This test runs the oldest test left in their deprecated test running
     * methods. All tests run in this function should be updated so this
     * function can be removed.
     *
     * @return null
     */
    static function test() {

        // Oldest style

        $testDirectory = CBSystemDirectory . '/snippets/tests';

        include "{$testDirectory}/Test,Colby,decrypt,encrypt.php"; // move to ColbyTests

        // Older style

        CBUnitTestsForCBDataStore::runAll();            // move to CBDataStoreTests
        CBUnitTestsForCBMarkaround::runAll();           // move to CBMarkaroundTests
        CBUnitTestsForColbyConvert::runAll();           // move to CBConvertTests
        CBUnitTestsForColbyMarkaroundParser::runAll();  // move to CBMarkaroundParserTests
    }
}

/**
 * This function is intended to be used with scalar values.
 *
 * @return null
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
