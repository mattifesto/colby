<?php

/**
 * @deprecated 2020_12_15
 *
 *      Use CBUI.createElement() with the "CBUI_section" class name.
 */
final class CBUISection {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v440.css', cbsysurl()),
        ];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v440.js', cbsysurl()),
        ];
    }
}
