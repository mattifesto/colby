<?php

final class
CBUIImageChooser
{
    /* -- CBHTMLOutput interfaces -- */

    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_CSSURLs(
    ): array
    {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.61.css',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_CSSURLs() */



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
                'v675.61.js',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */

}
/* CBUIImageChooser */
