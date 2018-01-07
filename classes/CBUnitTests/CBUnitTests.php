<?php

class CBUnitTests {

    /**
     * @param string $pageStub
     *
     * @return [string]
     */
    static function CBAdmin_menuNamePath(string $pageStub) {
        switch ($pageStub) {
            case 'AdminPageException':
                return ['develop', 'test'];

            default:
                return ['develop'];
        }
    }

    /**
     * @param string $pageStub
     *
     * @return void
     */
    static function CBAdmin_render(string $pageStub): void {
        switch ($pageStub) {
            case 'AdminPageException':
                throw new RuntimeException('Admin Page Exception Test');
                break;

            default:
                break;
        }
    }

    /**
     * @return null
     */
    static function CBAjax_errorTest() {
        //throw new RuntimeException(str_repeat("This is a test of a long message. ", 1000));
        throw new RuntimeException('Test exception thrown inside the PHP implementation of CBUnitTests::CBAjax_errorTest()');
    }

    /**
     * @return string
     */
    static function CBAjax_errorTest_group() {
        return 'Developers';
    }

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
            ['CBMessageMarkup',         'markupToText'],
            ['CBMessageMarkup',         'singleLineMarkupToText'],
            ['CBMessageMarkup',         'stringToMarkup'],
            ['CBModel',                 'toModel'],
            ['CBModel',                 'toModelMinimalImplementation'],
            ['CBModelCache'],
            ['CBModels',                'fetchModelByID'],
            ['CBModels',                'fetchModelsByID'],
            ['CBModels',                'saveNullableModel'],
            ['CBModels',                'saveSpecWithoutID'],
            ['CBPageLayout',            'CBModel_toModel'],
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
