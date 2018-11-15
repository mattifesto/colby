<?php

final class CBEvent {

    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v467.js', cbsysurl()),
        ];
    }
}
