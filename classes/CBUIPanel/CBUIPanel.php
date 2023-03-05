<?php

final class
CBUIPanel
{
    /* -- CBHTMLOutput interfaces -- */



    /**
     * @return string
     */
    static function
    CBHTMLOutput_CSSURLs(
    ): string
    {
        $className =
        __CLASS__;

        $fileVersionNumber =
        '2022_06_21_1655821960';

        $fileExtension =
        'css';

        $libraryPath =
        cbsysurl();

        $cssURL =
        CBLibrary::buildLibraryClassFilePath(
            $className,
            $fileVersionNumber,
            $fileExtension,
            $libraryPath
        );

        return $cssURL;
    }
    /* CBHTMLOutput_CSSURLs() */



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
                '2023_03_05_1677991324',
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
    ): array {
        return [
            'CBAjaxResponse',
            'CBConvert',
            'CBErrorHandler',
            'CBException',
            'CBMessageMarkup',
            'CBModel',
            'CBUI',

            'CBContentStyleSheet',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */

}
/* CBUIPanel */
