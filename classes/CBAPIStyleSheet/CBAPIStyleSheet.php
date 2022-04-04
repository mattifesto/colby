<?php

final class
CBAPIStyleSheet
{
    /* -- CBHTMLOutput interfaces -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_CSSURLs(
    ): array
    {
        return
        [
            Colby::flexpath(
                __CLASS__,
                'v675.69.css',
                cbsysurl()
            )
        ];
    }
    // CBHTMLOutput_CSSURLs()

}
