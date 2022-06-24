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
    ): array {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.60.js',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_requiredClassNames(
    ): array {
        return [
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
