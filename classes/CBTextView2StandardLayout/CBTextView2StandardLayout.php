<?php

/**
 * @deprecated 2018.01.28 This appears to be deprecated. Do research to find out
 * if it's used in certain view instances.
 */
final class
CBTextView2StandardLayout
{
    // -- CBHTMLOutput interfaces



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_CSSURLs(
    ): array
    {
        $arrayOfCSSURLs =
        [
            CBLibrary::buildLibraryClassFilePath(
                __CLASS__,
                '2023_03_19_1679253018',
                'css',
                cbsysurl()
            ),
        ];

        return $arrayOfCSSURLs;
    }
    // CBHTMLOutput_CSSURLs()

}
