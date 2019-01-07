<?php

/**
 * @deprecated use CBUIStringsPart
 */
final class CBUITitleAndDescriptionPart {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v469.css', cbsysurl()),
        ];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v361.js', cbsysurl()),
        ];
    }
}
