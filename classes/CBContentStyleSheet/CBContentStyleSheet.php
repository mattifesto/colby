<?php

final class CBContentStyleSheet {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [Colby::flexpath(__CLASS__, 'v403.css', cbsysurl())];
    }
}
