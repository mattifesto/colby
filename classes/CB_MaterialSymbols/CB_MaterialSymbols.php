<?php

/**
 * This class was created to include the Google Material Symbols font. It is
 * used by adding it to the required class names for a class. See the online
 * documenation for this font for more information.
 */
final class
CB_MaterialSymbols
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
            'https://fonts.googleapis.com/css2' .
            '?family=Material+Symbols+Outlined' .
            ':opsz,wght,FILL,GRAD@48,400,0,0',

            CBLibrary::buildLibraryClassFilePath(
                __CLASS__,
                '2022_10_24_1666652650',
                'css',
                cbsysurl()
            ),
        ];

        return $arrayOfCSSURLs;
    }
    // CBHTMLOutput_CSSURLs()

}
