<?php

final class
CBUISpecSaver
{
    /* -- CBHTMLOutput interfaces -- */



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
                'v675.2.js',
                scliburl()
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
            'CBAjax',
            'CBErrorHandler',
            'CBException',
            'CBModel',
            'CBUIPanel',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */

}
