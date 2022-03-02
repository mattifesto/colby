<?php

final class
CB_Documentation_ArtworkCollectionView
{
    /* -- CBHTMLOutput interfaces -- */



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
    ): array
    {
        return [
            'CBMessageView',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */

}
