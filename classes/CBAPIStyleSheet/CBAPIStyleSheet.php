<?php

final class CBAPIStyleSheet {

    /* -- CBHTMLOutput interfaces -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v374.css', cbsysurl())
        ];
    }

}
