<?php

final class CBContentStyleSheet {

    /* -- CBHTMLOutput interfaces -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs(
    ) {
        return [
            Colby::flexpath(
                __CLASS__,
                'v648.css',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_CSSURLs() */

}
