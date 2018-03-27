<?php

final class CBContentStyleSheet {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [Colby::flexpath(__CLASS__, 'v405.css', cbsysurl())];
    }
}
