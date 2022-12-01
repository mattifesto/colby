<?php

final class
CBUISectionItem4
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
                '2022_12_01_1669859736',
                'css',
                cbsysurl()
            ),
        ];

        return $arrayOfCSSURLs;
    }
    // CBHTMLOutput_CSSURLs()



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array
    {
        return [
            Colby::flexpath(
                __CLASS__,
                'v368.js',
                cbsysurl()
            ),
        ];
    }



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_requiredClassNames(
    ): array
    {
        return [
            'CB_UI',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- functions -- */



    /**
     * @return void
     */
    static function renderOpen(): void {
        echo '<div class="CBUISectionItem4">';
    }

    /**
     * @return void
     */
    static function renderClose(): void {
        echo '</div>';
    }
}
