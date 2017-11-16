<?php

class CBUnitTests {

    /**
     * @return null
     */
    static function getListOfTestsForAjax() {
        $response = new CBAjaxResponse();
        $response->tests = [
            ['CBConvert',               'linesToParagraphs'],
            ['CBConvert',               'textToLines'],
            ['CBConvert',               'textToStub'],
            ['CBConvert',               'valueAsInt'],
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
            ['CBLog',                   'noClassName'],
            ['CBLog',                   'noMessage'],
            ['CBMarkaround',            'paragraphToHTML'],
            ['CBMessageMarkup',         'markupToHTML'],
            ['CBMessageMarkup',         'stringToMarkup'],
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
            ['CBViewPage',              'save'],
            ['CBPageVerificationTask',  'importThumbnailURLToImage'],
            ['CBPageVerificationTask',  'upgradeThumbnailURLToImage'],
            ['Colby',                   'encryption'],
            ['ColbyMarkaroundParser',   'orderedList'],
            ['ColbyMarkaroundParser',   'paragraph'],
            ['ColbyMarkaroundParser',   'unorderedList'],
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
}
