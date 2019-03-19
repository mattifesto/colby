<?php

final class CBEvent {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v470.js', cbsysurl()),
        ];
    }
}
