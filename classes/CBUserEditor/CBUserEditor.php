<?php

final class
CBUserEditor
{
    // -- CBHTMLOutput interfaces



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array
    {
        return
        [
            Colby::flexpath(
                __CLASS__,
                '2022_06_11_1654965169.js',
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
    ): array
    {
        return
        [
            'CBMessageView',
            'CBModel',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */

}
