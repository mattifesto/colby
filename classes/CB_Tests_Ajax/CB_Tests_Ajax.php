<?php

final class
CB_Tests_Ajax
{
    // -- CBHTMLOutput interfaces



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array
    {
        $javaScriptURLs =
        [
            CBLibrary::buildLibraryClassFilePath(
                __CLASS__,
                '2022_08_09_1660089575',
                'js',
                cbsysurl()
            ),
        ];

        return $javaScriptURLs;
    }
    // CBHTMLOutput_JavaScriptURLs()



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_requiredClassNames(
    ): array
    {
        $requiredClassNames =
        [
            'CBAjax',
            'CBAjaxResponse',
            'CBConvert',
            'CBException',
            'CBTest',
        ];

        return $requiredClassNames;
    }
    /* CBHTMLOutput_requiredClassNames() */



    // -- CBTest interfaces



    static function
    CBTest_getTests(
    ): array
    {
        $tests =
        [
            (object)
            [
                'name' =>
                'checkErrorAjaxResponseProperties'
            ],
            (object)
            [
                'name' =>
                'interfaceHasNotBeenImplemented'
            ],
        ];

        return $tests;
    }
    // CBTest_getTests()

}
