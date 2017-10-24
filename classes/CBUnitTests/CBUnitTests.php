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
            ['CBMarkaround',            'paragraphToHTML'],
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
            ['Colby',                   'encryption'],
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

        // Old style

        CBUnitTestsForColbyConvert::runAll();           // move to CBConvertTests
        CBUnitTestsForColbyMarkaroundParser::runAll();  // move to CBMarkaroundParserTests
    }
}
